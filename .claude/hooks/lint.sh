#!/bin/bash
# Check PHP syntax and style on every edited .php file.

file_path=$(jq -r '.tool_input.file_path // empty')

case "$file_path" in
  *.php) ;;
  *) exit 0 ;;
esac

cd "${CLAUDE_PROJECT_DIR:-$PWD}" || {
  echo "lint.sh: cannot access project directory" >&2
  exit 2
}

php_bin="${PHP_BIN:-php}"

if ! syntax_output=$("$php_bin" -l "$file_path" 2>&1); then
  echo "$syntax_output" >&2
  exit 2
fi

if [ -x vendor/bin/pint ]; then
  if ! style_output=$(vendor/bin/pint --test "$file_path" 2>&1); then
    echo "$style_output" >&2
    exit 2
  fi
fi

exit 0
