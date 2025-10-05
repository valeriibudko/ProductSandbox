## Description

Implement a program that synchronizes two folders: source and replica. The program should maintain a full, identical copy of source folder at replica folder.

Synchronization must be one-way: after the synchronization content of the replica folder should be modified to exactly match content of the source folder.

File creation/copying/removal operations should be logged to a file and to the console output.

## Run script

`
python3 -u "./sync_folder.py" ./source ./replica 2 2 ./logs/
`