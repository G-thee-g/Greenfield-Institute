<?php
function renderToastAndModal(): void { ?>
<div id="toast" class="toast"><span class="ms" id="toast-icon" style="font-size:20px">check_circle</span><span id="toast-msg"></span></div>
<div class="modal-overlay" id="modal">
  <div class="modal-box">
    <h3 class="text-lg font-semibold text-on-surface mb-2" id="modal-title">Confirm Action</h3>
    <p class="text-sm text-on-surface-variant mb-6" id="modal-body">Are you sure?</p>
    <div class="flex justify-end gap-3">
      <button class="btn-secondary" onclick="closeModal()">Cancel</button>
      <button class="btn-danger" id="modal-confirm">Confirm</button>
    </div>
  </div>
</div>
<?php }
?>
