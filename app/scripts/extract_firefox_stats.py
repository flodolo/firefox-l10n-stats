#! /usr/bin/env python3

import json
import os
import sys
from datetime import datetime
from urllib.parse import quote as urlquote
from urllib.request import urlopen


def main():
    # Read existing data
    data_folder = os.path.abspath(os.path.join(
        os.path.dirname(__file__), os.pardir, 'data'))
    data_file = os.path.join(data_folder, 'data.json')
    with open(data_file, 'r') as f:
        full_data = json.load(f)

    # Exit if the data is already available for the day
    day_key = datetime.now().strftime('%Y-%m-%d')
    if day_key in full_data:
        sys.exit(f'Data already available for {day_key}')

    # Get locales of Nightly builds
    url = 'https://hg.mozilla.org/mozilla-central/raw-file/default/browser/locales/all-locales'
    nightly_locales = []
    response = urlopen(url)
    for locale in response:
        locale = locale.rstrip().decode()
        if locale not in ['', 'en-US'] and locale not in nightly_locales:
            nightly_locales.append(locale)
    nightly_locales.sort()
    # Remove ja-JP-mac
    nightly_locales.remove('ja-JP-mac')

    query = '''
{
  firefox: project(slug: "firefox") {
    localizations {
        locale {
            code
        },
        totalStrings,
        missingStrings,
        approvedStrings,
        unreviewedStrings,
    }
  }
}
'''

    pontoon_locales = {}
    full_data[day_key] = {}

    try:
        url = 'https://pontoon.mozilla.org/graphql?query={}'.format(urlquote(query))
        response = urlopen(url)
        json_data = json.load(response)
        for project, project_data in json_data['data'].items():
            for element in project_data['localizations']:
                locale = element['locale']['code']
                pontoon_locales[locale] = {
                    'completion': round((float(element['totalStrings'] - element['missingStrings'])) / element['totalStrings'] * 100, 2),
                    'translated': element['approvedStrings'],
                    'missing': element['missingStrings'],
                    'suggestions': element['unreviewedStrings'],
                }

        for locale in nightly_locales:
            if not locale in pontoon_locales:
                print(f'Warning: {locale} not available in Pontoon')
            else:
                full_data[day_key][locale] = pontoon_locales[locale]

        with open(data_file, 'w') as f:
            json.dump(full_data, f)

    except Exception as e:
        print(e)

if __name__ == '__main__':
    main()
