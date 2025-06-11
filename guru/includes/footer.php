</main> <footer class="footer p-3 bg-dark text-light">
            <div class="container-fluid d-flex justify-content-center align-items-center">
                <small class="me-4">&copy; <?= date('Y'); ?> PSDH. All Rights Reserved.</small>
                
                <a href="tel:+62218765432" class="text-light me-3 text-decoration-none" title="Telepon">
                    <i class="fa-solid fa-phone"></i>
                </a>
                <a href="mailto:info@psdh.sch.id" class="text-light text-decoration-none" title="Email">
                    <i class="fa-solid fa-envelope"></i>
                </a>
            </div>
        </footer>

    </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const body = document.body;
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            body.classList.toggle('sidebar-collapsed');
        });
    }
});
</script>

</body>
</html>