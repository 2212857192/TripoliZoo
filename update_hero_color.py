import os
import re

files = [
    r'resources\views\vet\dashboard.blade.php',
    r'resources\views\vet\quarantine.blade.php',
    r'resources\views\vet\cases\field.blade.php',
    r'resources\views\vet\cases\hospital.blade.php',
    r'resources\views\vet\decisions\index.blade.php',
    r'resources\views\vet\referrals\autopsy.blade.php',
    r'resources\views\vet\referrals\treatment.blade.php'
]

def process_file(f):
    path = os.path.join(r"d:\TripoliZoo", f)
    if not os.path.exists(path):
        return
        
    with open(path, 'r', encoding='utf-8') as file:
        content = file.read()
        
    # Replace background in .page-hero
    content = re.sub(
        r'(\.page-hero\s*\{[^\}]*?background\s*:\s*)[^;]+(;)',
        r'\g<1>linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%)\g<2>',
        content,
        count=1
    )
    # Replace box-shadow in .page-hero
    content = re.sub(
        r'(\.page-hero\s*\{[^\}]*?box-shadow\s*:\s*)[^;]+(;)',
        r'\g<1>0 16px 48px rgba(30,58,30,0.35)\g<2>',
        content,
        count=1
    )
        
    with open(path, 'w', encoding='utf-8') as file:
        file.write(content)
    print(f"Updated hero in {path}")

for f in files:
    process_file(f)
