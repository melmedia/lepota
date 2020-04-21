<?php

namespace lepota\config;

// This namespace must be used in all configuration files

// phpcs:disable
/**
 * Configuration file can return array of parameters or modify base configuration.
 *
 * To overwrite base configuration:
 * return ['param' => 'value', ...];
 *
 * Modify base configuration (base means next in search chain):
 * namespace lepota\config;
 * return base()->extend(['param' => 'newValue', 'param1' => 'value1', ...]);
 *
 * modify only one parameter:
 * return base()->extend('param', 'newValue');
 *
 * Chaining supported:
 * return base()->extend(...)->append(...)->append(...);
 *
 *
 * Methods:
 * extend          replace base configuration parameter 'param' with 'newValue' (or added to array value):
 *                 ['param' => 1] + .extend(['param' => 2]) = ['param' => 2]
 *                 ['param' => [1]] + .extend(['param' => [2]]) = ['param' => [1, 2]]
 * merge           add 'newValue' to base configuration:
 *                 ['param' => 1] + .merge(['param' => 2]) = ['param' => [1, 2]]
 * replace         overwrite base configuration. More accurate, than extend: replace value by full path
 *                 ['param' => ['subparam' => [1, 2, 3]]] + .replace(['param', 'subparam'], [4, 5]) = ['param' => ['subparam' => [4, 5]]]
 * append          parameter 'param' will be added only if not defined already, or exception will be thrown
 * appendOrIgnore  parameter 'param' will be added only if not defined already, or will be ignored
 *
 * Configuration file search order:
 * params.php
 * env/$ENV/params.local.php --> env/$ENV/params.php --> partners/$PARTNER/params.php --> common/params.php
 *
 * components/db.php
 * env/$ENV/components/db.local.php --> env/$ENV/components/db.php --> partners/$PARTNER/components/db.php --> common/components/db.php
 *
 * Properties:
 *
 * properties.local.php, properties.php must return array:
 * return ['param' => 'value'];
 *
 * Values will be available with property('param') or property('param', 'defaultValue')
 */
// phpcs:enable


/**
 * Base configuration file loading
 *
 * @return ConfigFile
 */
function base()
{
    return ApplicationConfig::instance()->loadBaseFile(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file']);
}


/**
 * Get value from properties.local.php, properties.php
 *
 * @param string $property
 * @param mixed|null $default Value to return if no property is found, or null to throw ConfigError exception
 * @return mixed
 * @throws ConfigError
 */
function property($property, $default = null)
{
    return ApplicationConfig::instance()->property($property, $default);
}


/**
 * Application configuration support
 */
class ApplicationConfig
{
    use ParametersContainerTrait;

    protected const SOURCE_COMMON = 'common';
    protected const SOURCE_ENV = 'env';
    protected const SOURCE_PARTNERS = 'partners';

    protected const CATEGORY_COMPONENTS = 'components';
    protected const CATEGORY_MODULES = 'modules';

    /** Default env folder name */
    protected const DEFAULT_ENV_NAME = 'dev';

    /**
     * Base configuration search chain, last item is most base.
     * for env/DEV/components/db.php base is partners/yopolis.ru/components/db.php
     * for partners/yopolis.ru/components/db.php base is common/components/db.php
     * @var string[]
     */
    protected static $precedence = [self::SOURCE_ENV, self::SOURCE_PARTNERS, self::SOURCE_COMMON];
    protected static $validPartners = [
        'c7s',
    ];
    protected static $customPartnerMapping = [
    ];

    protected static $instance;

    /** @var string Путь к папке config4 включая её саму */
    protected $basePath;
    /** @var string dev | prod | staging */
    protected $envName;
    /** @var string yopolis.ru | participatory.co.uk | participatory.es */
    protected $partnerName;
    /** @var ParametersContainer */
    protected $params;

    /** @var array Настройки из properties.local.php, properties.php */
    protected $properties;


    public static function instance()
    {
        if (!self::$instance) {
            throw new ConfigError('No application configuration instance created');
        }

        return self::$instance;
    }

    /**
     * Загрузка конкретного партнёрского конфига
     *
     * @param string $partnerName
     * @param array $path Путь к конфигу (params.php это будет ['params'],
     * components/feature.php это ['components', 'feature'])
     * @return array|null Конфиг партнёра или null если такого не нашлось
     */
    public static function loadPartnerConfig($partnerName, $path)
    {
        $config = new self(CONFIG_DIR);
        $config->loadEnvName('env.txt')
            ->setPartnerName($partnerName)
            ->loadProperties();


        $paramsFile = $config->getPartnersPath(join(DIRECTORY_SEPARATOR, $path) . '.php');
        if (!file_exists($paramsFile)) {
            return null;
        }
        $params = require $paramsFile;
        return $params instanceof ConfigFile ? $params->toArray() : $params;
    }

    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->params = new ParametersContainer();
        self::$instance = $this;
    }

    /**
     * @param string $fileName
     * @return ApplicationConfig
     */
    public function loadEnvName($fileName)
    {
        $envPath = join(DIRECTORY_SEPARATOR, [CONFIG_DIR, $fileName]);
        $this->envName = file_exists($envPath) ? trim(file_get_contents($envPath)) : self::DEFAULT_ENV_NAME;
        return $this;
    }

    public function setEnvName(string $envName): self
    {
        $this->envName = $envName;
        return $this;
    }

    /**
     * @return ApplicationConfig
     */
    public function initPartnerName()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $domain = $_SERVER['HTTP_HOST'];
            if ($this->checkPartnerName($domain)) {
                $this->partnerName = $domain;
            } elseif (
                'staging' == $this->envName
                && isset(self::$customPartnerMapping[$domain])
                && $this->checkPartnerName(self::$customPartnerMapping[$domain])
            ) {
                $this->partnerName =  self::$customPartnerMapping[$domain];
            } elseif (defined('YII_DEBUG') && YII_DEBUG && $this->hasProperty('partnerName')) {
                $partnerName = $this->property('partnerName');

                if (!$this->checkPartnerName($partnerName)) {
                    throw new ConfigError("Partner $partnerName is not in allowed partners list");
                }
                $this->partnerName = $partnerName;
            }
        }

        if (!$this->partnerName) {
            $this->partnerName = $this->hasProperty('defaultPartnerName')
                ? $this->property('defaultPartnerName')
                : reset(self::$validPartners);
        }
        define('YOPOLIS_PARTNER', $this->partnerName);

        return $this;
    }

    /**
     * Используется для загрузки конфига конкретного партнёра
     * @param string $partnerName
     * @return $this
     */
    protected function setPartnerName($partnerName)
    {
        $this->partnerName = $partnerName;
        return $this;
    }

    /**
     * Проверка наличия партнера в списке доступных
     * @param $partnerName
     * @return bool
     */
    protected function checkPartnerName($partnerName)
    {
        return in_array($partnerName, self::$validPartners);
    }

    /**
     * Load properties from host-wide shell environment variables file /opt/environment.sh
     *
     * @return ApplicationConfig
     * @throws ConfigError
     */
    public function loadEnvironmentFile()
    {
        if (!is_array($this->properties)) {
            $this->properties = [];
        }
        if (file_exists('/opt/environment.sh')) {
            foreach (file('/opt/environment.sh') as $paramValue) {
                $paramValue = rtrim($paramValue);
                if (!$paramValue || '#' == $paramValue[0]) {
                    continue;
                }
                list($param, $value) = explode(
                    '=',
                    str_replace(
                        'export ',
                        '',
                        $paramValue
                    )
                );
                if (!$param || !$value) {
                    throw new ConfigError(
                        // phpcs:ignore Generic.Files.LineLength
                        "/opt/environment.sh format must be 'key=value', 'export key=value', lines started with # are ignored"
                    );
                }
                $this->properties[$param] = $value;
            }
        }

        return $this;
    }

    /**
     * Загружает переменные из properties.local.php или properties.php
     *
     * @return ApplicationConfig
     * @throws ConfigError
     */
    public function loadProperties()
    {
        if (file_exists($propertiesFile = $this->getEnvPath("properties.local.php"))) {
            $this->properties = array_merge($this->properties, require $propertiesFile);
        } elseif (file_exists($propertiesFile = $this->getEnvPath("properties.php"))) {
            $this->properties = array_merge($this->properties, require $propertiesFile);
        }

        if (!is_array($this->properties) || empty($this->properties)) {
            throw new ConfigError("No properties in properties.local.php, properties.php, something going wrong");
        }
        return $this;
    }

    /**
     * Возвращает переменную, загруженную из properties.php или properties.local.php
     *
     * @param string $property
     * @param mixed|null $default Значение по-умолчанию, если null - выбрасываем исключение ConfigError
     * @return mixed
     * @throws ConfigError
     */
    public function property($property, $default = null)
    {
        $propertyValue = null;
        if (isset($this->properties[$property])) {
            $propertyValue = $this->properties[$property];
        }

        if (null === $propertyValue) {
            $env = getenv($property);
            if (false !== $env) {
                $propertyValue = $env;
            }
        }

        if (null === $propertyValue) {
            if (null !== $default) {
                return $default;
            } else {
                throw new ConfigError(
                    // phpcs:ignore Generic.Files.LineLength
                    "Property $property is not defined properties.php, properties.local.php, /opt/environment.sh or environment variables"
                );
            }
        }

        return $propertyValue;
    }

    public function hasProperty($property)
    {
        return isset($this->properties[$property]) || false !== getenv($property);
    }

    /**
     * @param string $category components | modules
     * @return ApplicationConfig
     */
    public function loadFiles($category)
    {
        $commonPath = $this->getCommonPath($category);
        if (!is_dir($commonPath)) {
            return $this;
        }
        foreach (scandir($commonPath) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $configName = basename($file, '.php');
            foreach (self::$precedence as $source) {
                $config = $this->loadFile($source, $category, $configName, true);
                if (!$config) {
                    $config = $this->loadFile($source, $category, $configName);
                }
                if ($config) {
                    $this->params->merge([$category => [$configName => $config->toArray()]], true);
                    break;
                }
            }
        }
        return $this;
    }

    /**
     * Для переданного пути к файлу определяем базовый конфиг и загружаем его
     * Учитывается порядок ApplicationConfig::$precedence и расширение .local.php
     *
     * @param string $filePath Полный путь к конфигу, для которого надо найти базовый конфиг
     * @return null|ConfigFile
     * @throws ConfigError
     */
    public function loadBaseFile($filePath)
    {
        $path = pathinfo($filePath);
        $fileName = explode('.', $path['basename']);
        $configName = $fileName[0];
        if (3 == count($fileName) && 'local' == $fileName[1]) {
            // например, для db.local.php базовым файлом будет db.php в той же папке
            $baseFilePath = str_replace('.local.php', '.php', $filePath);
            if (file_exists($baseFilePath)) {
                return new ConfigFile($baseFilePath);
            }
        }
        $path = explode(DIRECTORY_SEPARATOR, $path['dirname']);

        // $source: common | env /dev? | partners /participatory.co.uk?
        // $category?: components | modules
        // $configName: feature | db ...

        $source = null;
        $category = null;

        if ($index = array_search(self::SOURCE_COMMON, $path)) {
            // это вариант common/[components/]/db.php
            $source = $path[$index];
            $index++;
            if ($index < count($path)) {
                $category = $path[$index];
            }
        } elseif (
            ($index = array_search(self::SOURCE_ENV, $path))
            || ($index = array_search(self::SOURCE_PARTNERS, $path))
        ) {
            // это вариант env/dev/[components/]/db.php
            $source = $path[$index];
            $index += 2;    // пропускаем dev|prod|staging|yopolis.ru|...
            if ($index < count($path)) {
                $category = $path[$index];
            }
        }

        for (; $source = $this->ancestor($source);) {
            if (null === $source) {
                break;
            }
            $config = $this->loadFile($source, $category, $configName);
            if (null !== $config) {
                return $config;
            }
        }

        throw new ConfigError("Не найден базовый конфиг для $filePath");
    }

    /**
     * Возвращает имя базового источника @see ApplicationConfig::$precedence
     *
     * @param string $source common | env | partners
     * @return string|null Возвращаем null в случае, если запросили базовый конфиг для самого базового (common)
     * или source не из массива precedence
     */
    protected function ancestor($source)
    {
        $index = array_search($source, self::$precedence);
        if (false === $index) {
            return null;
        }
        $index++;
        if ($index >= count(self::$precedence)) {
            return null;
        }
        return self::$precedence[$index];
    }

    public function getCommonPath($appendix)
    {
        return join(DIRECTORY_SEPARATOR, $appendix ? [$this->basePath, self::SOURCE_COMMON, $appendix] :
            [$this->basePath, self::SOURCE_COMMON]);
    }

    public function getEnvPath($appendix = null)
    {
        return join(DIRECTORY_SEPARATOR, $appendix ?
            [$this->basePath, self::SOURCE_ENV, $this->envName, $appendix] :
            [$this->basePath, self::SOURCE_ENV, $this->envName]);
    }

    public function getPartnersPath($appendix = null)
    {
        return join(
            DIRECTORY_SEPARATOR,
            $appendix ?
                [$this->basePath, self::SOURCE_PARTNERS, $this->partnerName, $appendix] :
                [$this->basePath, self::SOURCE_PARTNERS, $this->partnerName]
        );
    }

    /**
     * Собираем путь к конфигу из параметров настроек окружения и загружаем
     *
     * @param string $source common | env | partners
     * @param string|null $category components | modules
     * @param string $config Имя конкретного конфига (без .php)
     * @param bool $isLocal Загружаем локальную версию .local
     * @return null|ConfigFile
     */
    protected function loadFile($source, $category, $config, $isLocal = false)
    {
        $path = [$this->basePath, $source];
        switch ($source) {
            case self::SOURCE_ENV:
                $path[] = $this->envName;
                break;

            case self::SOURCE_PARTNERS:
                $path[] = $this->partnerName;
                break;
        }
        if ($category) {
            $path[] = $category;
        }
        $path[] = $isLocal ? "{$config}.local" : $config;

        $filePath = join(DIRECTORY_SEPARATOR, $path) . '.php';
        return file_exists($filePath) ? new ConfigFile($filePath) : null;
    }
}
