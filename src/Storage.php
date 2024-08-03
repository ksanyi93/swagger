<?php
namespace SecretServer;

class Storage {
    private $file = __DIR__ . '\fruits.json';

    public function save($hash, $data)
    {
        date_default_timezone_set('Europe/Budapest');

        $currentDate = new \DateTime();
        $data['created_at'] = $currentDate->format('Y-m-d H:i:s');
        $currentDate->modify('+1 day');
        $data['expiresAt'] = $currentDate->format('Y-m-d H:i:s');
        
        $secrets = $this->loadAll();
        $secrets[$hash] = $data;
        file_put_contents($this->file, json_encode($secrets));
    }

    public function load($hash)
    {
        $secrets = $this->loadAll();
        return $secrets[$hash] ?? null;
    }

    public function delete($secret)
    {
        if ($secret['remainingViews'] != 0) {
            $secrets = $this->loadAll();
            $secrets[$secret['hash']]['remainingViews'] -= 1;
            isset($_SESSION['remainingViews']) ? $_SESSION['remainingViews'] -= 1 : '';
            file_put_contents($this->file, json_encode($secrets));
        }
        
        if (isset($secrets[$secret['hash']]['remainingViews']) && $secrets[$secret['hash']]['remainingViews'] == 0) {
            unset($secrets[$secret['hash']]);
            session_unset();
            session_destroy();
        }
    }

    private function loadAll()
    {
        if (file_exists($this->file)) {
            return json_decode(file_get_contents($this->file), true);
        }

        return [];
    }
}
