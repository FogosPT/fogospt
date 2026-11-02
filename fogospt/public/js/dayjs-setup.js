// Registers dayjs plugins. Loaded with defer right after dayjs and its plugins,
// so it runs (in order) before DOMContentLoaded and any $(document).ready code.
dayjs.extend(window.dayjs_plugin_utc);
