<footer class="footer">
    <p>&copy; <?= date('Y'); ?> Delux Spa | Panel Cliente</p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>

<?php
if (isset($pageScript) && file_exists($pageScript)) {
    echo '<script src="' . $pageScript . '"></script>';
}
?>
</body>

</html>