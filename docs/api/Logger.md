# Logger

## Namespace

`Retrologin`

## Methods

| Method | Description |
|--------|-------------|
| `getInstance` Logger | Get the singleton instance. |
| `debug($$message, $$context)`  | Log a debug message. Debug messages are used for detailed information useful during development and debugging. Only logged when RETRORLOGIN_DEBUG is true. |
| `info($$message, $$context)`  | Log an informational message. Info messages are used for general operational information about the plugin's normal operation. |
| `warning($$message, $$context)`  | Log a warning message. Warning messages indicate potential issues that don't prevent the plugin from functioning but may need attention. |
| `error($$message, $$context)`  | Log an error message. Error messages indicate failures that should be addressed. These are always logged regardless of debug settings. |
| `log($$level, $$message, $$context)`  | Internal log method. Handles the actual logging with structured format. |
| `isDebugEnabled` bool | Check if debug logging is enabled. |

