<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUVATION - Ekosistem Inovasi Pendidikan Provinsi Sulawesi Tenggara</title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('dashboard/Pemprov Sultra.png') ?>">
    <script src="<?= base_url('dashboard/tailwind.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('dashboard/fontawesome/css/all.min.css') ?>">
    <script src="<?= base_url('dashboard/chart.js') ?>"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0284c7',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .active-tab {
            border-bottom: 3px solid #0284c7;
            color: #0284c7;
            font-weight: 700;
        }

        /* Custom Scrollbar for smooth prototype experience */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>