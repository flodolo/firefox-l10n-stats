#! /usr/bin/env python3

import json
import os
import requests
import sys
from datetime import datetime


def main():
    # Read existing data
    data_folder = os.path.abspath(
        os.path.join(os.path.dirname(__file__), os.pardir, "data")
    )
    data_file = os.path.join(data_folder, "data.json")
    with open(data_file, "r") as f:
        full_data = json.load(f)

    # Exit if the data is already available for the day
    day_key = datetime.now().strftime("%Y-%m-%d")
    if day_key in full_data:
        sys.exit(f"Data already available for {day_key}")

    # Get locales of Nightly builds
    url = "https://raw.githubusercontent.com/mozilla-firefox/firefox/refs/heads/main/browser/locales/all-locales"
    nightly_locales = []
    response = requests.get(url)
    response.raise_for_status()
    for locale in response.iter_lines():
        locale = locale.rstrip().decode()
        if locale not in ["", "en-US"] and locale not in nightly_locales:
            nightly_locales.append(locale)
    nightly_locales.sort()
    # Remove ja-JP-mac
    nightly_locales.remove("ja-JP-mac")

    pontoon_locales = {}
    full_data[day_key] = {}

    try:
        url = "https://pontoon.mozilla.org/api/v2/projects/firefox"
        page = 1
        while url:
            print(f"Reading data (page {page})")
            response = requests.get(url)
            response.raise_for_status()
            data = response.json()

            for locale_data in data.get("localizations", []):
                locale = locale_data["locale"]["code"]
                pontoon_locales[locale] = {
                    "completion": round(
                        (
                            float(
                                locale_data["total_strings"]
                                - locale_data["missing_strings"]
                            )
                        )
                        / locale_data["total_strings"]
                        * 100,
                        2,
                    ),
                    "translated": locale_data["approved_strings"],
                    "missing": locale_data["missing_strings"],
                    "suggestions": locale_data["unreviewed_strings"],
                }
            # Get the next page URL
            url = data.get("next")
            page += 1
    except requests.RequestException as e:
        print(f"Error fetching data: {e}")
        sys.exit()

    for locale in nightly_locales:
        if locale not in pontoon_locales:
            print(f"Warning: {locale} not available in Pontoon")
        else:
            full_data[day_key][locale] = pontoon_locales[locale]

    with open(data_file, "w") as f:
        json.dump(full_data, f)


if __name__ == "__main__":
    main()
