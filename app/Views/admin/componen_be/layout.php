<?php include 'style.php' ?>

<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen font-sans">
    <?php include 'header.php' ?>
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 py-6">
        <?= $this->renderSection('content') ?>
    </main>
    <!-- footer -->
    <?php include 'footer.php'; ?>
    <!-- modals and scripts -->
    <?php include 'modal.php'; ?>
    <?php include 'script.php'; ?>
</body>

</html>