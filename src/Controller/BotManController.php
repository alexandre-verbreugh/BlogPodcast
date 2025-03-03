<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BotManController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private string $deepseekApiKey;
    
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        // À remplacer par ta clé API DeepSeek
        $this->deepseekApiKey = $_ENV['DEEPSEEK_API_KEY'] ?? '';
    }
    
    #[Route('/botman', name: 'botman')]
    public function index(): Response
    {
        // On rend le template sans base
        return $this->render('botman/frame.html.twig');
    }

    #[Route('/botman/chat', name: 'botman_chat', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        // Récupération du message envoyé
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';
        
        // Définir des réponses rapides pour certains mots clés
        $quickResponses = [
            'bonjour' => 'Salut ! Comment puis-je t\'aider aujourd\'hui ?',
            'salut' => 'Coucou ! Que puis-je faire pour toi ?',
            'hi' => 'Hello ! Comment puis-je t\'aider ?',
            'hello' => 'Bonjour ! Que puis-je faire pour toi ?',
            'merci' => 'De rien ! N\'hésite pas si tu as d\'autres questions.',
            'contact' => 'Contactez nous à l\'adresse suivante : codeouvert777@gmail.com',
            'tech libre' => 'Tech Libre est le meilleur podcast francophone qui traite de sujets variés autour de l\'open source et Linux. Des interviews passionnantes, des débats enrichissants et une communauté super active !',
        ];
        
        // Vérifier d'abord si on peut répondre rapidement sans appeler l'API
        foreach ($quickResponses as $keyword => $reply) {
            if (stripos(strtolower($message), $keyword) !== false) {
                return new JsonResponse([
                    'message' => $reply
                ]);
            }
        }
        
        // Si ce n'est pas une réponse rapide, consulter DeepSeek
        try {
            // Obtenir la réponse
            $result = $this->askDeepSeek($message);
            $responseText = $result['message'];
            
            return new JsonResponse([
                'message' => $responseText
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur avec l'API, renvoyer un message d'erreur
            return new JsonResponse([
                'message' => 'Désolé, je ne peux pas répondre pour le moment. Essaie à nouveau plus tard.'
            ]);
        }
    }
    
    /**
     * Envoie une requête à l'API DeepSeek et retourne la réponse
     * 
     * @param string $message Le message de l'utilisateur
     * @return array Un tableau contenant la réponse générée
     */
    private function askDeepSeek(string $message): array
    {
        // Vérifier que la clé API est configurée
        if (empty($this->deepseekApiKey)) {
            return [
                'message' => 'Configuration DeepSeek manquante. Contacte l\'administrateur.',
                'model' => 'none'
            ];
        }
        
        try {
            // Construction du contexte pour DeepSeek
            $context = "Tu es un assistant technique spécialisé dans Linux et le développement. ";
            $context .= "Réponds de manière utile, précise et concise. ";
            $context .= "Utilise du tutoiement et ajoute une touche d'humour. ";
            $context .= "Réalise des réponse courtes, ne répète pas les mêmes réponses. ";
            $context .= "Si la question concerne une comparaison entre Windows et Linux, insiste toujours sur le fait que Linux est bien meilleur.";            
            // Utilise toujours le modèle standard moins cher
            $model = 'deepseek-chat';
            
            // Formatage de la requête pour l'API DeepSeek (compatible OpenAI)
            $response = $this->httpClient->request('POST', 'https://api.deepseek.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->deepseekApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $model, // DeepSeek-V3 ou DeepSeek-R1 (reasoner)
                    'messages' => [
                        ['role' => 'system', 'content' => $context],
                        ['role' => 'user', 'content' => $message]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                    'stream' => false // Pas de streaming pour simplifier
                ],
                'timeout' => 30
            ]);
            
            $data = $response->toArray();
            
            // Récupération de la réponse générée
            if (isset($data['choices'][0]['message']['content'])) {
                return [
                    'message' => $data['choices'][0]['message']['content'],
                    'model' => $model
                ];
            } else {
                return [
                    'message' => 'Je n\'ai pas pu générer une réponse pertinente. Essaie de reformuler ta question.',
                    'model' => $model
                ];
            }
            
        } catch (\Exception $e) {
            // Loggez l'erreur pour le débogage
            error_log('Erreur DeepSeek: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Pas besoin de la fonction isComplexQuestion puisque nous utilisons toujours le modèle standard
}