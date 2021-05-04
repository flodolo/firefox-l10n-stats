#! /usr/bin/env python3

import json
import os
from urllib.request import urlopen


def main():
    # Get absolute path of ../data from current script location (not current folder)
    data_folder = os.path.abspath(
        os.path.join(os.path.dirname(__file__), os.pardir, "data")
    )

    release_locales = []
    url = "https://hg.mozilla.org/mozilla-central/raw-file/default/browser/locales/shipped-locales"
    excluded_locales = [
        "", "en-US", "ja-JP-mac"
    ]

    try:
        response = urlopen(url)
        for locale in response:
            locale = locale.rstrip().decode()
            if locale not in excluded_locales and locale not in release_locales:
                release_locales.append(locale)
        release_locales.sort()

        with open(os.path.join(data_folder, "release_locales.json"), "w") as f:
            json.dump(release_locales, f, sort_keys=True, indent=2)
    except Exception as e:
        print(e)


if __name__ == "__main__":
    main()
