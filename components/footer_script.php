<!-- FOOTER SCRIPTS -->
<script src="assets/js/api.js?v=4"></script>
<?php if (!empty($extraJs)): ?>
<?php foreach ((array)$extraJs as $js): ?>
<script src="<?= $js ?>?v=4"></script>
<?php endforeach; ?>
<?php endif; ?>
<script src="assets/js/app.js?v=4"></script>
</body>
</html>
