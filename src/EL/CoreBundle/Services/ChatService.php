<?php

namespace EL\CoreBundle\Services;

class ChatService
{
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
     * Create links
     * 
     * @param string $m
     * 
     * @return string
     */
    private function createExternalLinks($m)
    {
        return preg_replace('/https?:\/\/[\w\-\.!~#?&=+\*\'"(),\/]+/u', '<a href="$0" target="_blank">$0</a>', $m);
    }
}
