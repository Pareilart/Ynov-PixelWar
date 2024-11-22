<template>
    <div class="flex flex-col items-center p-6 bg-white rounded-lg shadow-lg">
        <!-- Section des outils -->
        <div class="flex items-center space-x-6 mb-6 p-4 bg-gray-50 rounded-lg w-full">
            <div class="flex flex-col items-center">
                <label class="text-sm text-gray-600 mb-2">Couleur</label>
                <input 
                    type="color" 
                    v-model="selectedColor" 
                    class="w-12 h-12 p-0 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 transition-colors"
                />
            </div>
            
            <div class="flex-1 bg-gray-100 p-3 rounded-md">
                <h3 class="font-medium text-gray-700 mb-2">Instructions :</h3>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li class="flex items-center">
                        <span class="mr-2">🖱️</span> Clic gauche pour ajouter un pixel
                    </li>
                    <li class="flex items-center">
                        <span class="mr-2">✨</span> Clic droit pour supprimer un pixel
                    </li>
                </ul>
            </div>
        </div>

        <!-- Canvas avec une bordure plus élégante -->
        <canvas 
            ref="canvas" 
            width="500" 
            height="500" 
            class="border-4 border-gray-200 rounded-lg shadow-md" 
            @click="handleCanvasClick"
            @contextmenu.prevent="handleCanvasClick" 
            style="image-rendering: pixelated"
        ></canvas>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const canvas = ref(null);
const pixels = ref([]);
const selectedColor = ref('#000000');

const fetchPixels = async () => {
    try {
        const response = await axios.get(route('pixels.index'));
        pixels.value = response.data;
        drawPixels();
    } catch (error) {
        console.error('Erreur lors de la récupération des pixels:', error);
    }
};

const addPixel = async (x, y) => {
    try {
        const response = await axios.post(route('pixels.store'), {
            x,
            y,
            color: selectedColor.value
        });
        pixels.value.push(response.data);
        drawPixel(response.data);
    } catch (error) {
        console.error('Erreur lors de l\'ajout du pixel:', error);
    }
};

const drawPixel = (pixel) => {
    if (!canvas.value) return;
    const ctx = canvas.value.getContext('2d');
    ctx.fillStyle = pixel.color;
    const pixelSize = 5;
    ctx.fillRect(pixel.x * pixelSize, pixel.y * pixelSize, pixelSize, pixelSize);
};

const drawPixels = () => {
    const ctx = canvas.value.getContext('2d');
    ctx.clearRect(0, 0, canvas.value.width, canvas.value.height);
    pixels.value.forEach(pixel => drawPixel(pixel));
};

const deletePixel = async (pixelId) => {
    try {
        const pixel = pixels.value.find(p => p.id === pixelId);
        if (!pixel) return;

        await axios.delete(route('pixels.destroy', pixel.id));

        pixels.value = pixels.value.filter(p => p.id !== pixel.id);

        drawPixels();
    } catch (error) {
        console.error('Erreur lors de la suppression du pixel:', error);
    }
};

const handleCanvasClick = (event) => {
    event.preventDefault();
    const rect = canvas.value.getBoundingClientRect();
    const pixelSize = 5;
    const x = Math.floor((event.clientX - rect.left) / pixelSize);
    const y = Math.floor((event.clientY - rect.top) / pixelSize);
    const pixelId = pixels.value.find(p => p.x === x && p.y === y)?.id;

    if (event.button === 2) {
        deletePixel(pixelId);
    } else {
        addPixel(x, y);
    }
};

onMounted(() => {
    fetchPixels();

    window.Echo.channel('post-pixel-create')
        .listen('PostPixelCreateEvent', (e) => {
            console.log('Nouveau pixel reçu:', e);
            pixels.value.push(e.pixel);
            drawPixel(e.pixel);
        });

    window.Echo.channel('post-pixel-delete')
        .listen('PostPixelDeleteEvent', (e) => {
            console.log('Pixel supprimer:', e);
            pixels.value = pixels.value.filter(p => p.id !== e.pixelId);
            drawPixels();
        });
});
</script>