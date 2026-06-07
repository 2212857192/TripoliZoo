import os
import re

path = r"D:\TripoliZoo\tripolizoo1\lib\features\visitor\visitor_home\presentation\home_screen.dart"

with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace alignments for RTL compatibility
content = content.replace("CrossAxisAlignment.end", "CrossAxisAlignment.start")
content = content.replace("TextAlign.right", "TextAlign.start")
# For MainAxisAlignment.end, we need to be careful, but in this file it's used for the Emergency section header
content = content.replace("MainAxisAlignment.end", "MainAxisAlignment.start")

# Let's also check if there's any 'arrow_back_ios_new' used where it should be 'arrow_forward_ios'
# If the app is RTL, iOS back arrow points right. So it's fine.

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Alignment fixed for RTL.")
