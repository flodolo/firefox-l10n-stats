#! /usr/bin/env python3

import json
import os
import requests


def main():
    # Get absolute path of ../data from current script location (not current folder)
    data_folder = os.path.abspath(
        os.path.join(os.path.dirname(__file__), os.pardir, "data")
    )

    release_locales = []
    url = "https://raw.githubusercontent.com/mozilla-firefox/firefox/refs/heads/release/browser/locales/shipped-locales"
    excluded_locales = ["", "en-US", "ja-JP-mac"]

    json_file = os.path.join(data_folder, "locales.json")
    with open(json_file, "r") as f:
        json_data = json.load(f)

    try:
        response = requests.get(url)
        response.raise_for_status()
        for locale in response.iter_lines():
            locale = locale.rstrip().decode()
            if locale not in excluded_locales and locale not in release_locales:
                release_locales.append(locale)
        release_locales.sort()

        json_data["release"] = release_locales
        with open(json_file, "w") as f:
            json.dump(json_data, f, sort_keys=True, indent=2)
    except Exception as e:
        print(e)


if __name__ == "__main__":
    main()
