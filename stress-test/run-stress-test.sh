#!/usr/bin/env bash
# Stress Test Runner (Linux/macOS)
# Usage:
#   ./stress-test/run-stress-test.sh
#   VUS=20 DURATION=3m ./stress-test/run-stress-test.sh

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BASE_URL="${BASE_URL:-http://localhost:8000/api}"
VUS="${VUS:-10}"
DURATION="${DURATION:-2m}"
DRIVER_EMAIL="${DRIVER_EMAIL:-stresstest-driver@example.com}"
DRIVER_PASSWORD="${DRIVER_PASSWORD:-password123}"

if ! command -v k6 &> /dev/null; then
    echo ""
    echo "k6 belum terinstall."
    echo "Install: https://k6.io/docs/get-started/installation/"
    echo "  macOS : brew install k6"
    echo "  Linux : sudo gpg -k && sudo gpg --no-default-keyring --keyring /usr/share/keyrings/k6-archive-keyring.gpg --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys C5AD17C747E3415A3642D57D77C6C491D6AC1D69 && echo \"deb [signed-by=/usr/share/keyrings/k6-archive-keyring.gpg] https://dl.k6.io/deb stable main\" | sudo tee /etc/apt/sources.list.d/k6.list && sudo apt-get update && sudo apt-get install k6"
    echo ""
    exit 1
fi

echo "Menjalankan stress test..."
echo "  Base URL : ${BASE_URL}"
echo "  VUs      : ${VUS}"
echo "  Duration : ${DURATION}"
echo ""

export BASE_URL VUS DURATION DRIVER_EMAIL DRIVER_PASSWORD
k6 run "${SCRIPT_DIR}/k6-stress-test.js"
