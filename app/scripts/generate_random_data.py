#! /usr/bin/env python3

import json
import os
import random
import sys
from datetime import datetime, timedelta


def main():
    # Read existing data
    data_folder = os.path.abspath(
        os.path.join(os.path.dirname(__file__), os.pardir, "data")
    )
    data_file = os.path.join(data_folder, "data.json")

    locales = [
        "ach",
        "af",
        "an",
        "ar",
        "ast",
        "az",
        "be",
        "bg",
        "bn",
        "bo",
        "br",
        "brx",
        "bs",
        "ca",
        "ca-valencia",
        "cak",
        "ckb",
        "cs",
        "cy",
        "da",
        "de",
        "dsb",
        "el",
        "en-CA",
        "en-GB",
        "eo",
        "es-AR",
        "es-CL",
        "es-ES",
        "es-MX",
        "et",
        "eu",
        "fa",
        "ff",
        "fi",
        "fr",
        "fy-NL",
        "ga-IE",
        "gd",
        "gl",
        "gn",
        "gu-IN",
        "he",
        "hi-IN",
        "hr",
        "hsb",
        "hu",
        "hy-AM",
        "hye",
        "ia",
        "id",
        "is",
        "it",
        "ja",
        "ka",
        "kab",
        "kk",
        "km",
        "kn",
        "ko",
        "lij",
        "lo",
        "lt",
        "ltg",
        "lv",
        "meh",
        "mk",
        "mr",
        "ms",
        "my",
        "nb-NO",
        "ne-NP",
        "nl",
        "nn-NO",
        "oc",
        "pa-IN",
        "pl",
        "pt-BR",
        "pt-PT",
        "rm",
        "ro",
        "ru",
        "scn",
        "si",
        "sk",
        "sl",
        "son",
        "sq",
        "sr",
        "sv-SE",
        "szl",
        "ta",
        "te",
        "th",
        "tl",
        "tr",
        "trs",
        "uk",
        "ur",
        "uz",
        "vi",
        "wo",
        "xh",
        "zh-CN",
        "zh-TW",
    ]

    full_data = {}
    previous_day = ""
    initial_day = current_day = datetime.now()
    for x in range(0, 400, 5):
        day_data = {}
        for locale in locales:
            if previous_day == "":
                # Generate completely random data
                translated = random.randrange(12000)
                missing = random.randrange(5000)
                suggestions = random.randrange(500)
                completion = round(100 * float(translated) / (missing + translated), 2)
            else:
                # Only do a variation of the existing data, to avoid making
                # a mess with the graphs
                previous_data = full_data[previous_day.strftime("%Y-%m-%d")][locale]
                translated = previous_data["translated"] + random.randrange(-10, 13)
                if translated < 0:
                    translated = random.randrange(6)
                missing = previous_data["missing"] + random.randrange(-2, 8)
                if missing < 0:
                    missing = random.randrange(6)
                suggestions = previous_data["suggestions"] + random.randrange(-4, 12)
                if suggestions < 0:
                    suggestions = random.randrange(6)
                completion = round(100 * float(translated) / (missing + translated), 2)
            day_data[locale] = {
                "completion": completion,
                "translated": translated,
                "missing": missing,
                "suggestions": suggestions,
            }
        previous_day = current_day
        current_day = initial_day - timedelta(days=x)
        full_data[current_day.strftime("%Y-%m-%d")] = day_data

    with open(data_file, "w") as f:
        json.dump(full_data, f)


if __name__ == "__main__":
    main()
