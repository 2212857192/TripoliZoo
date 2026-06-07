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

modal_css = """
    /* ═══ SIDE PANEL & OTHERS ═══ */
    .side-panel { display: flex; flex-direction: column; gap: 1.2rem; }
    .activity-feed { background: #fff; border-radius: 18px; border: 1px solid #e8edf5; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.04); }
    .activity-item { display: flex; gap: 12px; padding: 1rem 1.2rem; border-bottom: 1px solid #f8fafc; align-items: flex-start; transition: background 0.15s; }
    .activity-item:last-child { border-bottom: none; }
    .activity-item:hover { background: #f8faff; }
    .activity-dot { width: 10px; height: 10px; border-radius: 50%; margin-top: 4px; flex-shrink: 0; }
    .activity-text { flex: 1; font-size: 0.8rem; font-weight: 600; color: #334155; line-height: 1.5; }
    .activity-time { font-size: 0.7rem; color: #94a3b8; font-weight: 600; white-space: nowrap; margin-top: 2px; }

    /* ═══ QUICK LINKS ═══ */
    .quick-links { background: #fff; border-radius: 18px; border: 1px solid #e8edf5; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.04); }
    .quick-link-item { display: flex; align-items: center; gap: 12px; padding: 0.9rem 1.2rem; text-decoration: none; border-bottom: 1px solid #f8fafc; transition: all 0.2s; }
    .quick-link-item:last-child { border-bottom: none; }
    .quick-link-item:hover { background: #f0fdf4; padding-right: 1.5rem; }
    .quick-link-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .quick-link-text { flex: 1; font-size: 0.83rem; font-weight: 700; color: #334155; }
    .quick-link-arr { color: #cbd5e1; transition: color 0.2s, transform 0.2s; }
    .quick-link-item:hover .quick-link-arr { color: #1a4a2e; transform: translateX(-4px); }

    /* ═══ MODAL ═══ */
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; }
    .modal-backdrop.open { display: flex; }
    .modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; box-shadow: 0 30px 80px rgba(0,0,0,0.25); animation: modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
    @keyframes modalIn { from { transform: translateY(30px) scale(0.96); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
    .modal-header { padding: 1.4rem 1.6rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #0d2818, #1a4a2e); border-radius: 20px 20px 0 0; }
    .modal-header h3 { font-size: 1.05rem; font-weight: 800; color: #fff; }
    .modal-close { width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.15); border: none; color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1.1rem; font-weight: 700; transition: background 0.2s; }
    .modal-close:hover { background: rgba(255,255,255,0.25); }
    .modal-body { padding: 1.6rem; }
    .modal-footer { padding: 1rem 1.6rem; border-top: 1px solid #f1f5f9; display: flex; gap: 10px; justify-content: flex-end; }
    
    .decision-box { background: #fff; border-radius: 20px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 30px 80px rgba(0,0,0,0.25); animation: modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
    .decision-header { padding: 1.4rem 1.6rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius: 20px 20px 0 0; }
    .decision-header h3 { font-size: 1.05rem; font-weight: 800; color: #fff; }
    .decision-body { padding: 1.6rem; }
    .decision-footer { padding: 1rem 1.6rem; border-top: 1px solid #f1f5f9; display: flex; gap: 10px; justify-content: flex-end; }
    .decision-options { display: flex; flex-direction: column; gap: 10px; }
    .decision-option { display: flex; align-items: center; gap: 15px; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.2s; }
    .decision-option:hover { border-color: #94a3b8; background: #f8fafc; }
    .decision-option.selected { border-color: #2E7D32; background: #F0FDF4; }
    .opt-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    .opt-title { font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
    .opt-desc { font-size: 0.78rem; color: #64748b; font-weight: 600; line-height: 1.4; }
    .detail-section { margin-bottom: 1.5rem; }
    .detail-section h4 { display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem; padding-bottom: 8px; border-bottom: 2px solid #f1f5f9; }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .detail-item { display: flex; flex-direction: column; gap: 4px; }
    .detail-item label { font-size: 0.75rem; color: #64748b; font-weight: 700; }
    .detail-item span { font-size: 0.88rem; color: #0f172a; font-weight: 800; }
    .vet-note { background: #f8fafc; border-left: 3px solid #3b82f6; padding: 12px 15px; border-radius: 0 8px 8px 0; margin-bottom: 10px; }
    .note-date { font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 4px; }
    .note-text { font-size: 0.85rem; color: #334155; font-weight: 600; line-height: 1.5; }

    /* ═══ FORM ═══ */
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: span 2; }
    .form-group label { font-size: 0.8rem; font-weight: 800; color: #374151; }
    .form-group label span.req { color: #ef4444; }
    .form-input, .form-select, .form-textarea { padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: #0f172a; background: #fafbff; transition: border-color 0.2s, box-shadow 0.2s; outline: none; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: #2d7a47; box-shadow: 0 0 0 3px rgba(45,122,71,0.1); background: #fff; }
    .form-textarea { resize: vertical; min-height: 80px; }
    .btn-submit { padding: 10px 24px; background: linear-gradient(135deg, #1a4a2e, #2d7a47); color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(45,122,71,0.3); }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(45,122,71,0.35); }
    .btn-cancel { padding: 10px 20px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.88rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-cancel:hover { background: #e2e8f0; }

    /* ═══ CONFIRM MODAL ═══ */
    .confirm-box, .release-box { background: #fff; border-radius: 18px; width: 100%; max-width: 420px; padding: 2rem; text-align: center; box-shadow: 0 30px 80px rgba(0,0,0,0.2); animation: modalIn 0.3s cubic-bezier(0.34,1.56,0.64,1); }
    .confirm-icon, .release-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.6rem; }
    .confirm-icon { background: #fff1f2; }
    .release-icon { background: #f0fdf4; }
    .confirm-box h3, .release-box h3 { font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; }
    .confirm-box p, .release-box p { font-size: 0.83rem; color: #64748b; font-weight: 600; margin-bottom: 1.5rem; line-height: 1.6; }
    .confirm-actions { display: flex; gap: 10px; justify-content: center; }
    .btn-confirm-delete, .btn-confirm-slaughter { padding: 9px 22px; background: #e11d48; color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-confirm-delete:hover, .btn-confirm-slaughter:hover { background: #be123c; }
    .btn-confirm-release { padding: 9px 22px; background: #16a34a; color: #fff; border: none; border-radius: 10px; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 800; cursor: pointer; transition: all 0.2s; }
    .btn-confirm-release:hover { background: #15803d; }
"""

new_th_css = """
    .premium-table th {
        background: linear-gradient(135deg, #1e3a1e 0%, #2d5a27 100%);
        padding: 1rem 1.75rem;
        font-weight: 800; 
        font-size: 0.75rem; 
        color: #ffffff;
        border-bottom: 2px solid #1a4a2e;
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
        white-space: nowrap;
    }
"""

def process_file(f):
    path = os.path.join(r"d:\TripoliZoo", f)
    if not os.path.exists(path):
        return
        
    with open(path, 'r', encoding='utf-8') as file:
        content = file.read()
        
    # Replace the table header css
    # We look for the .premium-table th block
    content = re.sub(
        r'\.premium-table th\s*\{[^}]+\}',
        new_th_css.strip(),
        content
    )
    
    # Append the missing modal CSS before </style> if not already there
    if ".modal-backdrop" not in content:
        content = content.replace("</style>", modal_css + "\n</style>")
        
    with open(path, 'w', encoding='utf-8') as file:
        file.write(content)
    print(f"Fixed {path}")

for f in files:
    process_file(f)
