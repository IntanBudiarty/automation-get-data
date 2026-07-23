FROM python:3.10-slim

# Set working directory
WORKDIR /app

# Install system dependencies needed for Playwright Chromium
RUN apt-get update && apt-get install -y \
    curl \
    wget \
    gnupg \
    && rm -rf /var/lib/apt/lists/*

# Copy requirements and python files
COPY . /app

# Install python dependencies & playwright chromium with system dependencies
RUN pip install --no-cache-dir -r requirements.txt || pip install --no-cache-dir playwright requests
RUN playwright install chromium --with-deps

CMD ["python", "main.py", "--duration=30"]
