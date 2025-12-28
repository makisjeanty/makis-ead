{{-- Chat Widget de Suporte --}}
<div id="chatWidget" class="fixed bottom-6 right-6 z-50">
    {{-- Botão Flutuante --}}
    <button id="chatButton" onclick="toggleChat()" class="bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-full p-4 shadow-2xl hover:scale-110 transition-transform duration-300 flex items-center gap-2">
        <svg id="chatIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <span id="chatButtonText" class="hidden md:inline font-semibold">Aide</span>
    </button>

    {{-- Janela de Chat --}}
    <div id="chatWindow" class="hidden absolute bottom-20 right-0 w-96 max-w-[calc(100vw-3rem)] bg-white rounded-2xl shadow-2xl overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-purple-600 to-pink-500 text-white p-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                    <span class="text-2xl">💬</span>
                </div>
                <div>
                    <h3 class="font-bold">Support Étude Rapide</h3>
                    <p class="text-xs opacity-90">En ligne • Réponse rapide</p>
                </div>
            </div>
            <button onclick="toggleChat()" class="hover:bg-white/20 rounded-full p-2 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Chat Body --}}
        <div id="chatBody" class="h-96 overflow-y-auto p-4 bg-gray-50">
            {{-- Mensagem de Boas-vindas --}}
            <div class="mb-4">
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-gray-800 mb-2">👋 Bonjour! Comment puis-je vous aider?</p>
                    <p class="text-sm text-gray-600">Choisissez une question ou contactez-nous directement:</p>
                </div>
            </div>

            {{-- FAQ Buttons --}}
            <div class="space-y-2 mb-4">
                <button onclick="showAnswer('courses')" class="w-full text-left bg-white hover:bg-purple-50 rounded-lg p-3 shadow-sm transition">
                    <span class="text-purple-600 font-semibold">📚 Comment accéder à mes cours?</span>
                </button>
                
                <button onclick="showAnswer('payment')" class="w-full text-left bg-white hover:bg-purple-50 rounded-lg p-3 shadow-sm transition">
                    <span class="text-purple-600 font-semibold">💳 Comment fonctionne le paiement?</span>
                </button>
                
                <button onclick="showAnswer('cancel')" class="w-full text-left bg-white hover:bg-purple-50 rounded-lg p-3 shadow-sm transition">
                    <span class="text-purple-600 font-semibold">❌ Puis-je annuler mon abonnement?</span>
                </button>
                
                <button onclick="showAnswer('certificate')" class="w-full text-left bg-white hover:bg-purple-50 rounded-lg p-3 shadow-sm transition">
                    <span class="text-purple-600 font-semibold">🏆 Comment obtenir mon certificat?</span>
                </button>
            </div>

            {{-- Respostas (Hidden by default) --}}
            <div id="answer-courses" class="hidden mb-4">
                <div class="bg-purple-100 rounded-lg p-4">
                    <p class="text-gray-800 mb-2"><strong>📚 Accès aux cours:</strong></p>
                    <p class="text-sm text-gray-700">1. Connectez-vous à votre compte<br>2. Cliquez sur "Mes Cours" dans le menu<br>3. Sélectionnez le cours que vous souhaitez suivre</p>
                    <button onclick="hideAnswers()" class="mt-2 text-purple-600 text-sm font-semibold">← Retour</button>
                </div>
            </div>

            <div id="answer-payment" class="hidden mb-4">
                <div class="bg-purple-100 rounded-lg p-4">
                    <p class="text-gray-800 mb-2"><strong>💳 Paiement:</strong></p>
                    <p class="text-sm text-gray-700">Nous acceptons les paiements par carte de crédit via MercadoPago. Le paiement est sécurisé et instantané. Vous recevrez un email de confirmation.</p>
                    <button onclick="hideAnswers()" class="mt-2 text-purple-600 text-sm font-semibold">← Retour</button>
                </div>
            </div>

            <div id="answer-cancel" class="hidden mb-4">
                <div class="bg-purple-100 rounded-lg p-4">
                    <p class="text-gray-800 mb-2"><strong>❌ Annulation:</strong></p>
                    <p class="text-sm text-gray-700">Oui, vous pouvez annuler votre abonnement à tout moment sans frais ni pénalités. Accédez à "Mon Compte" → "Abonnement" → "Annuler".</p>
                    <button onclick="hideAnswers()" class="mt-2 text-purple-600 text-sm font-semibold">← Retour</button>
                </div>
            </div>

            <div id="answer-certificate" class="hidden mb-4">
                <div class="bg-purple-100 rounded-lg p-4">
                    <p class="text-gray-800 mb-2"><strong>🏆 Certificat:</strong></p>
                    <p class="text-sm text-gray-700">Vous recevez automatiquement votre certificat après avoir complété 100% des leçons du cours. Le certificat est disponible dans "Mes Cours" → "Certificats".</p>
                    <button onclick="hideAnswers()" class="mt-2 text-purple-600 text-sm font-semibold">← Retour</button>
                </div>
            </div>
        </div>

        {{-- Footer - Contact Buttons --}}
        <div class="p-4 bg-white border-t border-gray-200">
            <p class="text-sm text-gray-600 mb-3 text-center">Besoin d'aide personnalisée?</p>
            <div class="grid grid-cols-2 gap-2">
                <a href="https://wa.me/50937123456?text=Bonjour%2C%20j'ai%20besoin%20d'aide" target="_blank" class="flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    WhatsApp
                </a>
                <a href="mailto:support@etuderapide.com" class="flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Email
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function toggleChat() {
    const chatWindow = document.getElementById('chatWindow');
    const chatButton = document.getElementById('chatButton');
    
    if (chatWindow.classList.contains('hidden')) {
        chatWindow.classList.remove('hidden');
        chatWindow.classList.add('animate-fadeIn');
        chatButton.classList.add('scale-0');
    } else {
        chatWindow.classList.add('hidden');
        chatButton.classList.remove('scale-0');
        hideAnswers();
    }
}

function showAnswer(answerId) {
    hideAnswers();
    document.getElementById('answer-' + answerId).classList.remove('hidden');
    
    // Scroll to answer
    const chatBody = document.getElementById('chatBody');
    chatBody.scrollTop = chatBody.scrollHeight;
}

function hideAnswers() {
    const answers = ['courses', 'payment', 'cancel', 'certificate'];
    answers.forEach(id => {
        document.getElementById('answer-' + id).classList.add('hidden');
    });
}
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

#chatWidget button:focus {
    outline: none;
}
</style>
