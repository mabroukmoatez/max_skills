<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Facades\Storage;
use App\Models\Lesson; // Assurez-vous que le chemin vers votre modèle Lesson est correct

class ConvertVideoToHls extends Command
{
    /**
     * La signature de la commande.
     * {lesson : L'ID de la leçon à convertir}
     */
    protected $signature = 'video:convert-hls {lesson}';

    /**
     * La description de la commande.
     */
    protected $description = 'Convertit une vidéo de leçon au format HLS avec chiffrement AES-128';

    /**
     * Exécute la commande.
     */
    public function handle()
    {
        $lessonId = $this->argument('lesson');
        $lesson = Lesson::find($lessonId);

        if (!$lesson) {
            $this->error("Leçon avec l'ID {$lessonId} non trouvée.");
            return 1;
        }

        // Le chemin de la vidéo originale sur le disque 'public'
        // Ex: 'lessons_video/mon_fichier.mp4'
        $originalVideoPath = $lesson->path_video;

        if (!$originalVideoPath || !Storage::disk('public')->exists($originalVideoPath)) {
            $this->error("Fichier vidéo source non trouvé pour la leçon ID {$lessonId} au chemin : {$originalVideoPath}");
            return 1;
        }

        $this->info("Début de la conversion HLS pour : " . $originalVideoPath);

        // Génère un nom de dossier basé sur le nom du fichier original
        $baseName = pathinfo($originalVideoPath, PATHINFO_FILENAME);
        $hlsDirectory = 'hls/' . $baseName;

        // 1. Générer une nouvelle clé de chiffrement
        $encryptionKey = \ProtoneMedia\LaravelFFMpeg\Exporters\HLSExporter::generateEncryptionKey();

        // 2. Sauvegarder la clé sur notre disque sécurisé 'local' (qui pointe vers storage/app/private)
        // Le chemin sera par exemple : 'hls/mon_fichier/secret.key'
        Storage::disk('local')->put($hlsDirectory . '/secret.key', $encryptionKey);
        $this->info("Clé de chiffrement sauvegardée sur le disque 'local'.");

        // 3. Préparer l'URL que le lecteur vidéo utilisera pour demander la clé.
        // Cette URL doit correspondre à la route sécurisée que nous créerons plus tard.
        $keyUrl = route('video.hls.key', ['basename' => $baseName]);

        // 4. Lancer la conversion
        $this->info("Conversion en cours... Cela peut prendre plusieurs minutes.");
        try {
            FFMpeg::fromDisk('public')
                ->open($originalVideoPath)
                ->exportForHLS()
                ->withEncryptionKey($encryptionKey, $keyUrl) // Utilise la clé et fournit l'URL d'accès
                ->addFormat((new \FFMpeg\Format\Video\X264())->setKiloBitrate(500))  // Basse qualité 360p
                ->addFormat((new \FFMpeg\Format\Video\X264())->setKiloBitrate(1000)) // Qualité moyenne 480p
                ->addFormat((new \FFMpeg\Format\Video\X264())->setKiloBitrate(2500)) // Haute qualité 720p
                ->toDisk('public') // Sauvegarde les segments .ts et le manifeste .m3u8 sur le disque public
                ->save($hlsDirectory . '/playlist.m3u8');

        } catch (\Exception $e) {
            $this->error("Une erreur est survenue pendant la conversion FFMpeg :");
            $this->error($e->getMessage());
            return 1;
        }

        // 5. Mettre à jour la base de données pour stocker le chemin du manifeste HLS
        // Vous devez ajouter une colonne 'hls_playlist_path' à votre table 'lessons'
        $lesson->hls_playlist_path = $hlsDirectory . '/playlist.m3u8';
        $lesson->save();

        $this->info("Conversion HLS terminée avec succès pour la leçon ID: " . $lessonId);
        $this->info("Le manifeste est disponible sur le disque 'public' à l'adresse : " . $lesson->hls_playlist_path);
        
        return 0;
    }
}
