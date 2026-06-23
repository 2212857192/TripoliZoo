@if($group)
<div class="meta-note">
    المعرّف الحالي: <strong>#{{ $group->id }}</strong>
    — أي تعديل على الاسم يُحدّث الحيوانات والموظفين المرتبطين تلقائياً.
</div>
@endif

<div class="form-row">
    <div class="form-group">
        <label>اسم المجموعة <span style="color:#EF4444">*</span></label>
        <input type="text" name="name" class="form-input" value="{{ old('name', $group->name ?? '') }}" placeholder="مثال: الخيول" required>
        @error('name')<div style="color:#EF4444;font-size:0.82rem;margin-top:6px;">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label>بادئة رقم الحيوان <span style="color:#EF4444">*</span></label>
        <input type="text" name="code_prefix" class="form-input" value="{{ old('code_prefix', $group->code_prefix ?? '') }}" maxlength="10" placeholder="مثال: H" required>
        @error('code_prefix')<div style="color:#EF4444;font-size:0.82rem;margin-top:6px;">{{ $message }}</div>@enderror
        <p class="form-hint">حرف أو أكثر — يُستخدم لتوليد الرقم مثل H001.</p>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>ترتيب العرض <span style="color:#EF4444">*</span></label>
        <input type="number" name="sort_order" class="form-input" value="{{ old('sort_order', $group->sort_order ?? $nextSortOrder) }}" min="0" max="9999" required>
        @error('sort_order')<div style="color:#EF4444;font-size:0.82rem;margin-top:6px;">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label>حالة المجموعة</label>
        <div class="toggle-row">
            <label for="is_active">المجموعة نشطة ومتاحة في النظام</label>
            <label class="switch">
                <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $group->is_active ?? true))>
                <span class="slider"></span>
            </label>
        </div>
    </div>
</div>
