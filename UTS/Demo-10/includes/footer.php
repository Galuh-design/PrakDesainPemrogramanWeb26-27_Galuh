</main>

<footer>
    <div class="container_footer">
        <div class="box">
            <p>&copy; 2026 SIMPUS-Mini &mdash; ADAMSKA - UTS</p>
        </div>
    </div>

</footer>
<script src="<?php echo $base; ?>assets/js/app.js"></script>
<?php if (!empty($extra_scripts)):
    foreach ($extra_scripts as $src): ?>
        <script src="<?php echo $src; ?>"></script>
    <?php endforeach;
endif; ?>
</body>

</html>