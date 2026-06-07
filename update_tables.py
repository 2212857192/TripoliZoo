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

css_block = """    /* ═══ TABLE CARD ═══ */
    .data-card, .full-table-card, .table-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .data-card-header, .table-card-header {
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(to left, rgba(220, 252, 231, 0.95), #ffffff);
    }

    .data-card-title, .table-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #0f172a;
    }

    .view-all-link {
        font-size: 0.85rem;
        font-weight: 700;
        color: #16a34a;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .view-all-link:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    /* ═══ TABLE ═══ */
    .premium-table { width: 100%; border-collapse: separate; border-spacing: 0; text-align: right; }
    
    .premium-table th {
        background: rgba(200, 246, 218, 0.98);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 1rem 1.75rem;
        font-weight: 800; 
        font-size: 0.75rem; 
        color: #1a4a2e;
        border-bottom: 1px solid #bbf7d0;
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        white-space: nowrap;
    }

    .premium-table td {
        padding: 1.25rem 1.75rem;
        font-size: 0.9rem; 
        font-weight: 600;
        border-bottom: 1px solid #f1f5f9;
        color: #334155; 
        vertical-align: middle;
    }

    .premium-table tr:last-child td { border-bottom: none; }
    .premium-table tbody tr { transition: background-color 0.2s ease; }
    .premium-table tbody tr:hover td { background-color: #f8fafc; }

    /* ═══ BADGES ═══ */
    .badge {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        white-space: nowrap;
    }
    .badge .dot { width: 6px; height: 6px; border-radius: 50%; }

    .badge-treatment  { background: #eff6ff; color: #2563eb; }
    .badge-treatment .dot { background: #3b82f6; }
    .badge-autopsy    { background: #fef2f2; color: #dc2626; }
    .badge-autopsy .dot { background: #ef4444; }
    .badge-quarantine { background: #fff7ed; color: #ea580c; }
    .badge-quarantine .dot { background: #f97316; }
    .badge-hospital   { background: #f0fdf4; color: #16a34a; }
    .badge-hospital .dot { background: #22c55e; }
    .badge-pending   { background: #fef2f2; color: #e11d48; border: 1px solid #fecdd3; }
    .badge-pending .dot { background: #e11d48; }
    .badge-review    { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-review .dot { background: #d97706; }
    .badge-ready     { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-ready .dot { background: #15803d; }
    .badge-completed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-completed .dot { background: #15803d; }
    .badge-none { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .badge-none .dot { background: #94a3b8; }
    .badge-active   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-active .dot { background: #22c55e; }

    /* ═══ ACTION BUTTON ═══ */
    .btn-action, .btn-tbl {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; border-radius: 8px;
        font-family: 'Cairo', sans-serif; font-size: 0.8rem; font-weight: 700;
        cursor: pointer; text-decoration: none; transition: all 0.2s;
        border: 1px solid #e2e8f0; white-space: nowrap;
        background: #ffffff; color: #334155;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .btn-action:hover, .btn-tbl:hover {
        background: #f8fafc; border-color: #cbd5e1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-tbl-view { background: #fff; color: #334155; border-color: #e2e8f0; }
    .btn-tbl-view:hover { background: #f8fafc; border-color: #94a3b8; }
    .btn-tbl-export { background: #fff; color: #16a34a; border-color: #bbf7d0; }
    .btn-tbl-export:hover { background: #f0fdf4; border-color: #22c55e; }

    /* Animal ID style */
    .animal-id {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        background: #f8fafc;
        padding: 2px 6px;
        border-radius: 6px;
        color: #64748b;
        font-weight: 700;
        display: inline-block;
        margin-top: 4px;
        border: 1px solid #e2e8f0;
    }
"""

def update_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()

        # Find where table card starts
        match = re.search(r'/\*\s*═══\s*TABLE\s*CARD\s*═══\s*\*/', content)
        if not match:
            # Maybe it starts with TABLE
            match = re.search(r'/\*\s*═══\s*TABLE\s*═══\s*\*/', content)
        
        if not match:
            print(f"Skipped {filepath}, no table section found")
            return
            
        start_idx = match.start()
        
        # Find the end of styles (</style>)
        end_match = re.search(r'</style>', content[start_idx:])
        if not end_match:
            print(f"Skipped {filepath}, </style> not found after table section")
            return
            
        end_idx = start_idx + end_match.start()
        
        new_content = content[:start_idx] + css_block + content[end_idx:]
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")
    except Exception as e:
        print(f"Error on {filepath}: {e}")

for f in files:
    full_path = os.path.join(r"d:\TripoliZoo", f)
    if os.path.exists(full_path):
        update_file(full_path)
