<?php
$flash = getFlash();
?>
<?php if ($flash): ?>
    <div style="
        padding: 12px 16px;
        margin-bottom: 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        background: <?= $flash['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>;
        color: <?= $flash['type'] === 'success' ? '#155724' : '#721c24' ?>;
        border: 1px solid <?= $flash['type'] === 'success' ? '#c3e6cb' : '#f5c6cb' ?>;
    ">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>
