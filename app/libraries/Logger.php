<?php

class Logger
{
  private const LOG_FILE = 'test.log';
  private const DATE_FORMAT = 'Y-m-d H:i:s';
  private const LINE_SEPARATOR = "\n";
  private static $logPath;

  /**
   * Initialize log file path and ensure it's writable
   *
   * @return void
   * @throws Exception if log directory or file is not writable
   */
  private static function initialize(): void
  {
    if (!isset(self::$logPath))
    {
      $logsDir = './logs';

      if (!is_dir($logsDir))
      {
        if (!mkdir($logsDir, 0777, true))
        {
          throw new Exception('Cannot create logs directory');
        }
      }

      self::$logPath = $logsDir . DIRECTORY_SEPARATOR . self::LOG_FILE;

      if (!file_exists(self::$logPath))
      {
        touch(self::$logPath);
        chmod(self::$logPath, 0666);
      }

      if (!is_writable(self::$logPath))
      {
        throw new Exception('Log file is not writable: ' . self::$logPath);
      }
    }
  }

  /**
   * Format message for logging, handling both strings and arrays
   *
   * @param mixed $message The message to format (string or array)
   * @return string Formatted message
   */
  private static function formatMessage($message): string
  {
    if (is_array($message))
    {
      return json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    return (string)$message;
  }

  /**
   * Log a message to the file
   *
   * @param mixed $message The message to log (string or array)
   * @param string $level Log level (INFO, ERROR, WARNING, DEBUG)
   * @return bool True if logged successfully, false otherwise
   */
  public static function Log($message, string $level = 'INFO'): bool
  {
    try
    {
      self::initialize();

      $formattedMessage = self::formatMessage($message);
      $logEntry = self::formatLogEntry($formattedMessage, $level);

      // If message contains multiple lines, indent them for better readability
      if (str_contains($formattedMessage, "\n"))
      {
        $lines = explode("\n", $logEntry);
        $firstLine = array_shift($lines);
        $indentedLines = array_map(fn($line) => "    " . $line, $lines);
        $logEntry = $firstLine . "\n" . implode("\n", $indentedLines);
      }

      $result = file_put_contents(
        self::$logPath,
        $logEntry . self::LINE_SEPARATOR,
        FILE_APPEND | LOCK_EX
      );

      return $result !== false;
    } catch (Exception $e)
    {
      error_log("Failed to write to log file: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Format a log entry
   *
   * @param string $message The message to log
   * @param string $level Log level
   * @return string Formatted log entry
   */
  private static function formatLogEntry(string $message, string $level): string
  {
    $timestamp = date(self::DATE_FORMAT);
    $pid = getmypid();

    return sprintf(
      '[%s] [%s] [PID:%d] %s',
      $timestamp,
      strtoupper($level),
      $pid,
      $message
    );
  }

  /**
   * Clear the log file
   *
   * @return bool True if cleared successfully, false otherwise
   */
  public static function Clear(): bool
  {
    try
    {
      self::initialize();
      return file_put_contents(self::$logPath, '') !== false;
    } catch (Exception $e)
    {
      error_log("Failed to clear log file: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Get the full log content
   *
   * @return string|false The log content or false on failure
   */
  public static function GetContent()
  {
    try
    {
      self::initialize();
      return file_get_contents(self::$logPath);
    } catch (Exception $e)
    {
      error_log("Failed to read log file: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Get the log file size in bytes
   *
   * @return int|false The file size or false on failure
   */
  public static function GetSize()
  {
    try
    {
      self::initialize();
      return filesize(self::$logPath);
    } catch (Exception $e)
    {
      error_log("Failed to get log file size: " . $e->getMessage());
      return false;
    }
  }

  /**
   * Convenience method for logging errors
   *
   * @param mixed $message The error message (string or array)
   * @return bool True if logged successfully, false otherwise
   */
  public static function Error($message): bool
  {
    return self::Log($message, 'ERROR');
  }

  /**
   * Convenience method for logging warnings
   *
   * @param mixed $message The warning message (string or array)
   * @return bool True if logged successfully, false otherwise
   */
  public static function Warning($message): bool
  {
    return self::Log($message, 'WARNING');
  }

  /**
   * Convenience method for logging debug messages
   *
   * @param mixed $message The debug message (string or array)
   * @return bool True if logged successfully, false otherwise
   */
  public static function Debug($message): bool
  {
    return self::Log($message, 'DEBUG');
  }

  /**
   * Convenience method for logging info messages
   *
   * @param mixed $message The info message (string or array)
   * @return bool True if logged successfully, false otherwise
   */
  public static function Info($message): bool
  {
    return self::Log($message, 'INFO');
  }
}