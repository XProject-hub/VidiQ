# fetch_stats.py
import os

def system_stats():
    # Fetch some basic system stats like disk usage
    disk_usage = os.popen("df -h").read()
    return disk_usage

if __name__ == "__main__":
    print(system_stats())
