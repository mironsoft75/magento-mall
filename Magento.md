# Magento

## 开发环境配置

### 1. 安装 [XAMPP](https://www.apachefriends.org/download.html)

使用 `7.4.29` 版本，以便适配 magento

### 2. 安装 [Composer](https://getcomposer.org/download/)

下载安装即可

### 3. 修改 php.ini 配置

修改 `xampp/php/php.ini` 配置，将以下配置打开或修改

```ini
; 启用配置
extension=intl
extension=soap
extension=xsl
extension=sockets
extension=sodium
; 修改配置
max_execution_time=18000
memory_limit=4G
```

### 4. 执行 `composer install`

若 windows 系统的命令行环境不存在 `patch` 命令，可先将 git 目录下的 `usr/bin` 目录添加到环境变量中，以利用其的 `patch` 命令。

而后在项目根目录执行，安装命令

```bash
$ composer install
```

### 5. 修改 `Validator.php` 文件

打开 `vendor\magento\framework\View\Element\Template\File\Validator.php` 文件，添加如下代码（用以修复 windows 路径斜杠问题）

```php
protected function isPathInDirectories($path, $directories)
{
    if (!is_array($directories)) {
        $directories = (array)$directories;
    }
    $realPath = $this->fileDriver->getRealPath($path);
    $realPath = str_replace('\\', '/', $realPath); // 添加在这里
    foreach ($directories as $directory) {
        if (0 === strpos($realPath, $directory)) {
            return true;
        }
    }
    return false;
}
```

### 6. 启动项目

```bash
$ php -S 127.0.0.1:8082 -t ./pub/ ./phpserver/router.php
```

## 其他注意事项

1. 项目基础文档 `phpserver/README.md`
2. 错误日志 `var/log/exception.log`，大部分问题可根据异常日志解决
3. 若修改了 `env.php` 文件，需要重新执行命令 `$ php bin/magento app:config:import`

## 参考文档

> [How To Install Magento 2 on Windows](https://ccbill.com/kb/install-magento-windows)
>
> [Magento2提示错误 Invalid template file解决方法](https://www.liuxds.com/blog/show/magento2-Invalid-template-file)
