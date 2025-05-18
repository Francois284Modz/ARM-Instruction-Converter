<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ARM Encoder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">

    <div class="bg-gray-800 p-8 rounded-xl shadow-lg w-full max-w-2xl">
        <h1 class="text-2xl font-bold mb-6 text-center">ARM Instruction Encoder</h1>

        <form id="encodeForm" class="space-y-4">
            <div>
                <label class="block mb-1 font-semibold">Architecture</label>
                <select name="architecture" class="w-full p-2 rounded bg-gray-700 text-white">
                    <option value="ARM32">ARM32</option>
                    <option value="ARM64">ARM64</option>
                </select>
            </div>

            <div>
                <label class="block mb-1 font-semibold">Type</label>
                <select name="type" id="typeSelect" class="w-full p-2 rounded bg-gray-700 text-white">
                    <option value="INT">INT</option>
                    <option value="FLOAT">FLOAT</option>
                    <option value="BOOLEAN">BOOLEAN</option>
                    <option value="INT_RANGE">INT RANGE</option>
                    <option value="FLOAT_RANGE">FLOAT RANGE</option>
                </select>
            </div>

            <!-- Single Value input -->
            <div id="singleValueContainer">
                <label class="block mb-1 font-semibold">Single Value</label>
                <input type="number" name="singleValue" class="w-full p-2 rounded bg-gray-700 text-white">
            </div>

            <!-- BOOLEAN CHECKBOXES -->
            <div id="booleanContainer" class="hidden space-y-2">
                <span class="font-semibold">Boolean Value</span>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="boolTrue" class="form-checkbox text-green-500">
                        <span>True</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="boolFalse" class="form-checkbox text-red-500">
                        <span>False</span>
                    </label>
                </div>
            </div>

            <!-- Range inputs -->
            <div id="rangeContainer" class="hidden space-y-2">
                <label class="block mb-1 font-semibold">Start Value</label>
                <input type="number" name="startValue" class="w-full p-2 rounded bg-gray-700 text-white">
                <label class="block mb-1 font-semibold">End Value</label>
                <input type="number" name="endValue" class="w-full p-2 rounded bg-gray-700 text-white">
                <label class="block mb-1 font-semibold">Step Value</label>
                <input type="number" name="stepValue" class="w-full p-2 rounded bg-gray-700 text-white">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded">
                Encode
            </button>
        </form>

        <div id="result" class="mt-6 hidden grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Big endian card -->
            <div class="bg-gray-700 p-4 rounded shadow relative">
                <h2 class="font-bold text-lg mb-2">Big Endian</h2>
                <button onclick="copyResult('resultBig')" class="absolute top-2 right-2 text-sm bg-blue-500 hover:bg-blue-600 px-2 py-1 rounded">Copier</button>
                <pre id="resultBig" class="whitespace-pre-wrap break-all"></pre>
            </div>

            <!-- Little endian card -->
            <div class="bg-gray-700 p-4 rounded shadow relative">
                <h2 class="font-bold text-lg mb-2">Little Endian</h2>
                <button onclick="copyResult('resultLittle')" class="absolute top-2 right-2 text-sm bg-blue-500 hover:bg-blue-600 px-2 py-1 rounded">Copier</button>
                <pre id="resultLittle" class="whitespace-pre-wrap break-all"></pre>
            </div>
        </div>
    </div>

    <script>
        const typeSelect = document.getElementById('typeSelect');
        const singleValueContainer = document.getElementById('singleValueContainer');
        const booleanContainer = document.getElementById('booleanContainer');
        const rangeContainer = document.getElementById('rangeContainer');

        const boolTrue = document.getElementById('boolTrue');
        const boolFalse = document.getElementById('boolFalse');

        boolTrue.addEventListener('change', () => {
            if (boolTrue.checked) boolFalse.checked = false;
        });
        boolFalse.addEventListener('change', () => {
            if (boolFalse.checked) boolTrue.checked = false;
        });

        typeSelect.addEventListener('change', () => {
            const selected = typeSelect.value;
            // Réinitialise les cases à chaque changement de type
                boolTrue.checked = false;
                boolFalse.checked = false;

            if (selected === 'BOOLEAN') {
                singleValueContainer.classList.add('hidden');
                rangeContainer.classList.add('hidden');
                booleanContainer.classList.remove('hidden');
            } else if (selected === 'INT_RANGE' || selected === 'FLOAT_RANGE') {
                singleValueContainer.classList.add('hidden');
                booleanContainer.classList.add('hidden');
                rangeContainer.classList.remove('hidden');
            } else {
                singleValueContainer.classList.remove('hidden');
                booleanContainer.classList.add('hidden');
                rangeContainer.classList.add('hidden');
            }
        });

        document.getElementById('encodeForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            if (typeSelect.value === 'BOOLEAN') {
                const boolVal = boolTrue.checked ? '1' : '0';
                formData.set('singleValue', boolVal);
            }

            const response = await fetch('encoder.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            const resultDiv = document.getElementById('result');
            const resultBig = document.getElementById('resultBig');
            const resultLittle = document.getElementById('resultLittle');

            if (data.results) {
                const bigs = data.results.map(r => r.big_endian).join('\n');
                const littles = data.results.map(r => r.little_endian).join('\n');

                resultBig.textContent = bigs;
                resultLittle.textContent = littles;
            } else if (data.error) {
                resultBig.textContent = "Erreur : " + data.error;
                resultLittle.textContent = "";
            }

            resultDiv.classList.remove('hidden');
        });

        function copyResult(elementId) {
        const text = document.getElementById(elementId).textContent;
        navigator.clipboard.writeText(text)
            .then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Copié !',
                    text: 'Le contenu a été copié dans le presse-papiers.',
                    timer: 1500,
                    showConfirmButton: false
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de copier dans le presse-papiers.'
                });
            });
}

    </script>

</body>
</html>
