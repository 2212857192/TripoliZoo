@if (session('success'))
    <div style="background:#F0FDF4;border:1px solid #86EFAC;color:#166534;padding:12px 16px;border-radius:10px;margin-bottom:1.2rem;font-weight:700;font-size:0.9rem;">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div style="background:#FEF2F2;border:1px solid #FECACA;color:#991B1B;padding:12px 16px;border-radius:10px;margin-bottom:1.2rem;font-weight:700;font-size:0.9rem;">
        {{ session('error') }}
    </div>
@endif
