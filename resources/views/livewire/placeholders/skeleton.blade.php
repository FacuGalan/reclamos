<div>
    <style>
        .placeholder-container {
            padding: 1.5rem;
            background: white;
            border-radius: 8px;
        }

        /* Modo oscuro para el contenedor */
        .dark .placeholder-container {
            background: #374151; /* gray-700 */
        }

        .placeholder-line {
            height: 16px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 4px;
            margin-bottom: 12px;
            animation: shimmer 1.5s infinite;
        }

        /* Modo oscuro para las líneas del skeleton */
        .dark .placeholder-line {
            background: linear-gradient(90deg, #4b5563 25%, #6b7280 50%, #4b5563 75%);
            background-size: 200% 100%;
        }

        .placeholder-line:nth-child(1) { width: 100%; }
        .placeholder-line:nth-child(2) { width: 85%; animation-delay: 0.2s; }
        .placeholder-line:nth-child(3) { width: 70%; animation-delay: 0.4s; }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
    </style>

    <div class="placeholder-container">
        <div class="placeholder-line"></div>
        <div class="placeholder-line"></div>
        <div class="placeholder-line"></div>
    </div>
    <div class="placeholder-container">
        <div class="placeholder-line"></div>
        <div class="placeholder-line"></div>
        <div class="placeholder-line"></div>
    </div>
</div>