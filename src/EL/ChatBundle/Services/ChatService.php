<?php

namespace EL\ChatBundle\Services;

use Psr\Log\LoggerInterface as Log;
use Symfony\Component\Translation\TranslatorInterface;
use EL\CoreBundle\Entity\Player;
use EL\CoreBundle\Services\PlayerService;

class ChatService
{
    /**
     * @var PlayerService
     */
    private $playerService;
    
    /**
     *
     * @var TranslatorInterface
     */
    private $t;
    
    /**
     * @var boolean
     */
    private $logsEnabled;
    
    /**
     * @var string
     */
    private $logsDir;
    
    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(PlayerService $playerService, TranslatorInterface $t, $logsEnabled, $logsDir)
    {
        $this->playerService = $playerService;
        $this->t = $t;
        $this->logsEnabled = $logsEnabled;
        $this->logsDir = $logsDir;
        
        if ($logsEnabled) {
            $this->createChatLogDirectory();
        }
    }
    
    /**
     * Create a chat folder in logs directory
     */
    private function createChatLogDirectory()
    {
        if (!file_exists($this->logsDir.'/chat')) {
            mkdir($this->logsDir.'/chat');
        }
    }
    
    /**
     * @param \EL\CoreBundle\Entity\Player $player
     * 
     * @return string
     */
    public function getPlayerLink(Player $player)
    {
        return $this->playerService->getLink($player);
    }
    
    /**
     * Get translated message for a player join
     * 
     * @param \EL\CoreBundle\Services\Player $player
     * @param string $locale
     * 
     * @return string
     */
    public function getJoinMessage(Player $player, $locale)
    {
        return $this->t->trans('chat.join.%pseudo%', array('%pseudo%' => $player->getPseudo()), null, $locale);
    }
    
    /**
     * Get translated messge for a player leave
     * 
     * @param \EL\CoreBundle\Services\Player $player
     * @param string $locale
     * 
     * @return string
     */
    public function getLeaveMessage(Player $player, $locale)
    {
        return $this->t->trans('chat.leave.%pseudo%', array('%pseudo%' => $player->getPseudo()), null, $locale);
    }
    
    /**
     * Sanitize and add rich features to a chat message
     * 
     * @param string $m
     * 
     * @return string
     */
    public function parseMessage($m)
    {
        $m = $this->sanitizeMessage($m);
        $m = $this->createExternalLinks($m);
        $m = $this->createPlayerLink($m);
        
        return $m;
    }
    
    /**
     * Secure input
     * 
     * @param string $m
     * 
     * @return string
     */
    private function sanitizeMessage($m)
    {
        return htmlspecialchars($m, ENT_COMPAT, 'UTF-8');
    }
    
    /**
     * Transform urls into hyperlinks.
     * 
     * Example:
     * Go to http://domain.tld !
     * becomes:
     * Go to <a href="http://domain.tld" target="_blank">http://domain.tld</a> !
     * 
     * @param string $m
     * 
     * @return string
     */
    private function createExternalLinks($m)
    {
        return preg_replace('/https?:\/\/[\w\-\.!~#?&=+\*\'"(),\/]+/u', '<a href="$0" target="_blank">$0</a>', $m);
    }
    
    /**
     * Create links to players.
     * 
     * Example:
     * Hi @Pseudo !
     * becomes:
     * Hi <a href="#">@Pseudo</a> !
     * 
     * @param string $m
     */
    private function createPlayerLink($m)
    {
        $matches = array();
        preg_match_all('/@[a-z0-9]+/ui', $m, $matches);
        
        foreach ($matches[0] as $pseudo) {
            $player = $this->playerService->getPlayerByPseudoCI($pseudo);

            if ($player) {
                $playerLink = $this->playerService->getLink($player);
                $m = str_replace($pseudo, '<a href="'.$playerLink.'">@'.$player->getPseudo().'</a>', $m);
            }
        }
        
        return $m;
    }
    
    /**
     * Log message in logs
     * 
     * @param string $channel
     * @param string $message
     * @param Player|null $player or null for a server message
     */
    public function log($channel, $message, Player $player = null)
    {
        if (!$this->logsEnabled) {
            return;
        }
        
        $datetime = new \DateTime();
        
        $message = implode(' ', array(
            $datetime->format('Y-m-d H:i:s'),
            str_pad($player ? $player->getPseudo() : 'SERVER', 32),
            $message,
        )).PHP_EOL;
        
        $fileName = $this->logsDir.'/'.$channel.'.log';
        
        $file = fopen($fileName, 'a');
        fwrite($file, $message);
        fclose($file);
    }
}
