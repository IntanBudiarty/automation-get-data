import time
import json
import sys
import re
import argparse
import urllib.request
import urllib.parse
from playwright.sync_api import sync_playwright

sys.stdout.reconfigure(encoding='utf-8')

# Argument Parsing
parser = argparse.ArgumentParser(description="YouTube Shorts Playwright Automation Service")
parser.add_argument("--duration", type=int, default=30, help="Scroll duration in seconds")
parser.add_argument("--history_id", type=int, default=0, help="Automation History ID")
parser.add_argument("--api_url", type=str, default="http://127.0.0.1:8000/api/automation/callback", help="Backend API callback URL")

args = parser.parse_args()

DURATION = args.duration
HISTORY_ID = args.history_id
API_URL = args.api_url

videos = []
visited_urls = set()

def clean_text(text):
    if not text:
        return ""
    return " ".join(text.split()).strip()

def clean_url(raw_url):
    if not raw_url:
        return ""
    return raw_url.split('?')[0].split('&')[0].strip()

def is_valid_title(text):
    if not text:
        return False
    lower = text.lower()
    invalid_titles = ["comments", "komentar", "tidak ditemukan", "like", "dislike", "share", "bagikan"]
    if any(lower.startswith(kw) or lower == kw for kw in invalid_titles):
        return False
    return True

def is_valid_channel(text):
    if not text:
        return False
    lower = text.lower()
    invalid_channels = ["tidak ditemukan", "subscribe", "disubscribe", "langganan", "komentar", "comments"]
    if any(lower == kw for kw in invalid_channels):
        return False
    return True

def clean_channel_name(text):
    text = clean_text(text)
    if not text:
        return ""
    lines = text.split("\n")
    chan = lines[0].strip()
    for kw in ["Subscribe", "Subscribed", "Disubscribe", "Langganan"]:
        if chan.endswith(kw):
            chan = chan[:-len(kw)].strip()
    return chan

def get_views_data(page, active_reel):
    # 1. Check factoid or player header inside active reel
    try:
        if active_reel and active_reel.count() > 0:
            factoids = active_reel.locator("factoid-renderer, #factoid, ytd-shorts-player-header-renderer").all_inner_texts()
            for f in factoids:
                if f and any(kw in f.lower() for kw in ["view", "ditonton", "penonton", "tayangan"]):
                    return clean_text(f)
    except Exception:
        pass

    # 2. Check inner HTML regex match in active reel
    try:
        if active_reel and active_reel.count() > 0:
            html = active_reel.inner_html()
            m = re.search(r'(\d[\d\.,\s]*(?:[kKMmTt]|rb|jt)?\s*(?:x\s*)?(?:views|ditonton|penonton|tayangan))', html, re.IGNORECASE)
            if m:
                return clean_text(m.group(1))
    except Exception:
        pass

    # 3. Fallback evaluate ytInitialData
    try:
        data = page.evaluate("() => window.ytInitialData")
        if data:
            s = json.dumps(data)
            matches = re.findall(r'\"([0-9\.,\s]+(?:[kKMmTt]|rb|jt)?\s*(?:x\s*)?(?:views|ditonton|penonton|tayangan))\"', s, re.IGNORECASE)
            if matches:
                return clean_text(matches[0])
    except Exception:
        pass

    return "Tidak diketahui"

def get_active_short_data(page, max_retries=3):
    title = ""
    channel = ""
    views = ""

    for _ in range(max_retries):
        active_reel = page.locator("ytd-reel-video-renderer[is-active]")
        reel_target = active_reel if active_reel.count() > 0 else page.locator("ytd-reel-video-renderer").first

        # 1. Search inside active reel container
        if reel_target.count() > 0:
            if not is_valid_title(title):
                for title_sel in [
                    "yt-shorts-video-title-view-model span",
                    "yt-shorts-video-title-view-model h2",
                    "h2.yt-shorts-video-title-view-model",
                    "yt-shorts-video-title-view-model"
                ]:
                    try:
                        t_locs = reel_target.locator(title_sel)
                        for idx in range(t_locs.count()):
                            txt = clean_text(t_locs.nth(idx).inner_text(timeout=500))
                            if is_valid_title(txt):
                                title = txt
                                break
                    except Exception:
                        pass
                    if is_valid_title(title):
                        break

            if not is_valid_channel(channel):
                for chan_sel in [
                    "yt-reel-channel-bar-view-model a",
                    "yt-reel-channel-bar-view-model span",
                    "ytd-channel-name a",
                    "ytd-channel-name span"
                ]:
                    try:
                        c_locs = reel_target.locator(chan_sel)
                        for idx in range(c_locs.count()):
                            raw = c_locs.nth(idx).inner_text(timeout=500)
                            txt = clean_channel_name(raw)
                            if is_valid_channel(txt):
                                channel = txt
                                break
                    except Exception:
                        pass
                    if is_valid_channel(channel):
                        break

        # Fallback global title & channel search
        if not is_valid_title(title):
            for title_sel in ["yt-shorts-video-title-view-model span", "yt-shorts-video-title-view-model h2"]:
                try:
                    t_locs = page.locator(title_sel)
                    for idx in range(min(t_locs.count(), 5)):
                        txt = clean_text(t_locs.nth(idx).inner_text(timeout=500))
                        if is_valid_title(txt):
                            title = txt
                            break
                except Exception:
                    pass
                if is_valid_title(title):
                    break

        if not is_valid_channel(channel):
            for chan_sel in ["yt-reel-channel-bar-view-model a", "yt-reel-channel-bar-view-model span"]:
                try:
                    c_locs = page.locator(chan_sel)
                    for idx in range(min(c_locs.count(), 5)):
                        txt = clean_channel_name(c_locs.nth(idx).inner_text(timeout=500))
                        if is_valid_channel(txt):
                            channel = txt
                            break
                except Exception:
                    pass
                if is_valid_channel(channel):
                    break

        # Extract views count
        views = get_views_data(page, reel_target)

        if is_valid_title(title) and is_valid_channel(channel):
            return title, channel, views

        page.wait_for_timeout(1000)

    final_title = title if is_valid_title(title) else "Tidak ditemukan"
    final_channel = channel if is_valid_channel(channel) else "Tidak ditemukan"
    return final_title, final_channel, views or "Tidak diketahui"


def send_callback_to_backend(history_id, status, videos_data, api_url):
    if not videos_data and status == "completed":
        print("\n[Backend Info] Tidak ada data video yang dikirim.")
        return

    if history_id > 0:
        payload = {
            "history_id": history_id,
            "status": status,
            "videos": videos_data
        }
        target_url = api_url
    else:
        payload = {
            "videos": videos_data
        }
        if "automation/callback" in api_url:
            target_url = api_url.replace("automation/callback", "videos/batch")
        else:
            target_url = api_url

    json_data = json.dumps(payload).encode('utf-8')

    req = urllib.request.Request(
        target_url,
        data=json_data,
        headers={'Content-Type': 'application/json'}
    )

    try:
        with urllib.request.urlopen(req, timeout=10) as response:
            res_text = response.read().decode('utf-8')
            try:
                res_json = json.loads(res_text)
                msg = res_json.get('message', 'Berhasil disimpan')
                total = res_json.get('total_saved', len(videos_data))
                print(f"\n[Backend API] Status: OK - {msg} ({total} video).")
            except Exception:
                print("\n[Backend API] Status: OK - Response received.")
    except Exception as e:
        print(f"\n[Backend API Error] Gagal mengirim data ke Laravel: {e}")


print("=" * 60)
print(f" 🚀 STARTING YOUTUBE SHORTS AUTOMATION")
print(f" ⏱️  Durasi     : {DURATION} detik")
print(f" 🆔 History ID : {HISTORY_ID}")
print(f" 🔗 API URL    : {API_URL}")
print("=" * 60)

try:
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=False)
        page = browser.new_page()

        page.goto("https://www.youtube.com/shorts", wait_until="domcontentloaded", timeout=60000)
        page.wait_for_timeout(5000)

        start_time = time.time()
        while time.time() - start_time < DURATION:
            raw_url = page.url
            target_url = clean_url(raw_url)

            if target_url not in visited_urls and "/shorts/" in target_url:
                visited_urls.add(target_url)
                title, channel, views = get_active_short_data(page)

                if is_valid_title(title) and is_valid_channel(channel):
                    video_item = {
                        "title": title,
                        "channel": channel,
                        "url": target_url,
                        "views": views,
                        "scraped_at": time.strftime("%Y-%m-%d %H:%M:%S")
                    }
                    videos.append(video_item)

                    print(f"\n✅ Video #{len(videos)} Berhasil Diambil")
                    print(f"   📌 Judul   : {title}")
                    print(f"   👤 Channel : {channel}")
                    print(f"   👁️  Views   : {views}")
                    print(f"   🔗 URL     : {target_url}")
                    print("-" * 60)
                else:
                    print(f"\n⚠️  Skipped (Data tidak lengkap): Title='{title}', Channel='{channel}'")

            page.keyboard.press("ArrowDown")
            page.wait_for_timeout(3000)

        browser.close()

    print("\n" + "=" * 60)
    print(f" 📊 HASIL SCRAPING SELESAI ({len(videos)} Video Ditemukan)")
    print("=" * 60)
    print(json.dumps(videos, indent=4, ensure_ascii=False))

    print("\n=== JSON_OUTPUT_START ===")
    print(json.dumps(videos, ensure_ascii=False))
    print("=== JSON_OUTPUT_END ===")

    if API_URL:
        send_callback_to_backend(HISTORY_ID, "completed", videos, API_URL)

except Exception as err:
    print(f"\n❌ [Error] Automation gagal: {err}")
    print("\n=== JSON_OUTPUT_START ===")
    print(json.dumps([], ensure_ascii=False))
    print("=== JSON_OUTPUT_END ===")
    if API_URL:
        send_callback_to_backend(HISTORY_ID, "failed", [], API_URL)