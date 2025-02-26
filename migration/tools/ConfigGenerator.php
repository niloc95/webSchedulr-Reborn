<?php
namespace Migration\Tools;

class ConfigGenerator {
    private $timestamp = '2025-02-26 09:32:04';
    private $user = 'niloc95';
    private $baseTemplate = [
        'URL_CONFIG' => [
            'base_url' => '{{BASE_URL}}',
            'subfolder' => '{{SUBFOLDER}}',
            'protocol' => 'https'
        ],
        'PATH_CONFIG' => [
            'application' => 'application/',
            'public' => 'public/',
            'assets' => 'assets/',
            'uploads' => 'uploads/',
            'logs' => 'logs/'
        ],
        'APP_CONFIG' => [
            'app_name' => 'WebSchedulr',
            'app_version' => '2.0.0',
            'timezone' => 'UTC',
            'locale' => 'en_US',
            'debug_mode' => false
        ],
        'SECURITY_CONFIG' => [
            'encryption_key' => '{{ENCRYPTION_KEY}}',
            'session_timeout' => 3600,
            'cookie_secure' => true,
            'csrf_protection' => true
        ]
    ];

    public function generateConfig($environment = 'production') {
        $config = $this->baseTemplate;
        
        // Add environment-specific settings
        switch ($environment) {
            case 'development':
                $config['APP_CONFIG']['debug_mode'] = true;
                $config['SECURITY_CONFIG']['cookie_secure'] = false;
                break;
            
            case 'testing':
                $config['APP_CONFIG']['debug_mode'] = true;
                $config['APP_CONFIG']['app_name'] .= ' (Test)';
                break;
        }

        // Add generation metadata
        $config['METADATA'] = [
            'generated_at' => $this->timestamp,
            'generated_by' => $this->user,
            'environment' => $environment
        ];

        return $config;
    }

    public function writeConfigFile($config, $path) {
        $content = "<?php\n";
        $content .= "// Generated at: {$this->timestamp}\n";
        $content .= "// Generated by: {$this->user}\n\n";
        
        $content .= $this->arrayToPhp('CONFIG', $config);
        
        return file_put_contents($path, $content);
    }

    private function arrayToPhp($name, $array, $indent = 0) {
        $spaces = str_repeat('    ', $indent);
        $php = $spaces . "const $name = [\n";
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $php .= $this->arrayToPhp($key, $value, $indent + 1);
            } else {
                $php .= $spaces . "    '$key' => " . 
                    (is_string($value) ? "'$value'" : var_export($value, true)) . ",\n";
            }
        }
        
        $php .= $spaces . "];\n";
        return $php;
    }
}