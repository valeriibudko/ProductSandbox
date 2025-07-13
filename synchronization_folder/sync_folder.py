import os
import sys
import time
import shutil
import hashlib
from pathlib import Path
from datetime import datetime


def log(message, path_log):
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    timestamp_file = datetime.now().strftime('%Y-%m-%d')
    full_message = f"[{timestamp}] {message}"
    print(full_message)

    file_name_log = 'log_sync_folder_' + timestamp_file + '.log'
    full_path = os.path.join(path_log, file_name_log)
    with open(full_path, 'a') as log_file:
        log_file.write(full_message + '\n')


def calculate_md5(file_path):
    hash_md5 = hashlib.md5()
    with open(file_path, 'rb') as f:
        for chunk in iter(lambda: f.read(4096), b""):
            hash_md5.update(chunk)
    return hash_md5.hexdigest()


def get_relative_paths(root_path):
    all_paths = set()
    for dirpath, dirnames, filenames in os.walk(root_path):
        for filename in filenames:
            full_path = os.path.join(dirpath, filename)
            relative_path = os.path.relpath(full_path, root_path)
            all_paths.add(relative_path)
    return all_paths


def sync_folders(source, replica, path_log):
    source_paths = get_relative_paths(source)
    replica_paths = get_relative_paths(replica)

    # Create or update files from source to replica
    for relative_path in source_paths:
        source_file = os.path.join(source, relative_path)
        replica_file = os.path.join(replica, relative_path)

        source_md5 = calculate_md5(source_file)

        if not os.path.exists(replica_file):
            os.makedirs(os.path.dirname(replica_file), exist_ok=True)
            shutil.copy2(source_file, replica_file)
            log(f"File copied: {relative_path}", path_log)
        else:
            replica_md5 = calculate_md5(replica_file)
            if source_md5 != replica_md5:
                shutil.copy2(source_file, replica_file)
                log(f"File updated: {relative_path}", path_log)

    # Delete files in replica that are not in source
    for relative_path in replica_paths - source_paths:
        replica_file = os.path.join(replica, relative_path)
        os.remove(replica_file)
        log(f"File removed: {relative_path}", path_log)

    # Remove empty directories in replica
    for dirpath, dirnames, filenames in os.walk(replica, topdown=False):
        if not dirnames and not filenames:
            os.rmdir(dirpath)
            log(f"Directory removed: {os.path.relpath(dirpath, replica)}", path_log)


def main():
    if len(sys.argv) != 6:
        print("Error. Arguments is wrong. Usage: python sync_folder.py <path_source> <path_replica> <interval_sec> <synchronization_count> <path_log>")
        return
    
    source = sys.argv[1]
    replica = sys.argv[2]
    try:
        interval = int(sys.argv[3])
        sync_count = int(sys.argv[4])
    except ValueError:
        print("Error. Interval and synchronization count must be integers.")
        return
    
    if interval <= 0 or interval > 100:
        print("Error. Interval must be more 0 and less 100.")
        return
    
    if sync_count <= 0 or sync_count > 100:
        print("Error. Synchronization count must be more 0 and less 100.")
        return
    
    path_log = sys.argv[5]
    os.makedirs(path_log, exist_ok=True)
    os.makedirs(replica, exist_ok=True)

    log(f"*************************************************", path_log)
    log(f"Sync is started.", path_log)
    log(f"Argumets. Path source: {source}", path_log)
    log(f"Argumets. Path replica: {replica}", path_log)
    log(f"Argumets. Interval: {interval}", path_log)
    log(f"Argumets. Sync count: {sync_count}", path_log)
    
    for i in range(sync_count):
        log(f"Synchronization {i+1} of {sync_count}. Started.", path_log)
        sync_folders(source, replica, path_log)
        log(f"Synchronization {i+1} of {sync_count}. Completed.", path_log)
        # Sleep before next synchronization
        if i < sync_count - 1:
            log(f"Sleep for {interval} sec",path_log)
            time.sleep(interval)
    log(f"Sync is completed.", path_log)

if __name__ == "__main__":
    main()