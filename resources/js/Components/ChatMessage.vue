<template>
    <div class="flex flex-col h-[500px]">
        <div class="flex-1 overflow-y-auto mb-4 p-4 bg-gray-50 rounded-lg">
            <div v-for="message in messages" :key="message.id" class="mb-4">
                <div class="flex items-start">
                    <div class="flex-1">
                        <div class="flex items-center mb-1">
                            <span class="font-bold">{{ message.user }}</span>
                            <span class="text-xs text-gray-500 ml-2">{{ message.timestamp }}</span>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-lg">
                            {{ message.content }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-2">
            <input v-model="newMessage" @keyup.enter="sendMessage" type="text" placeholder="Écrivez votre message..."
                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <button @click="sendMessage"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Envoyer
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const messages = ref([]);
const newMessage = ref('');

const fetchMessages = async () => {
    try {
        const response = await axios.get(route('messages.index'));
        console.log(response.data);
        messages.value = response.data.messages.map(message => ({
            id: message.id,
            content: message.content,
            timestamp: new Date(message.created_at).toLocaleTimeString(),
            user: message.user.name,
        }));
    } catch (error) {
        console.error('Erreur lors de la récupération des messages:', error);
    }
};

onMounted(() => {
    fetchMessages();

    window.Echo.channel('post-message-create')
        .listen('PostMessageCreateEvent', (e) => {
            console.log('Nouveau message reçu:', e);
            if (e.message) {
                messages.value.push({
                    id: e.message.id,
                    content: e.message.content,
                    timestamp: new Date(e.message.created_at).toLocaleTimeString(),
                    user: e.message.user.name
                });
            }
        });
});

const sendMessage = async () => {
    if (newMessage.value.trim()) {
        try {
            await axios.post(route('messages.store'), {
                content: newMessage.value
            });
            newMessage.value = '';
        } catch (error) {
            console.error('Erreur lors de l\'envoi du message:', error);
        }
    }
};
</script>
