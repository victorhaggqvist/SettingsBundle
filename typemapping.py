#!/usr/bin/env python
import os
import sys

def main():
    if len(sys.argv) < 2:
        print('Generate core types switch cases')
        print('usage: {} path/to/form/component'.format(__file__))
        print('sample: {} vendor/symfony/symfony/src/Symfony/Component/Form'.format(__file__))
        exit(0)

    FORM_COMPONENT_ROOT = sys.argv[1]

    TYPES_DIR = 'Extension/Core/Type'
    DIR = os.path.join(FORM_COMPONENT_ROOT, TYPES_DIR)
    types = os.listdir(DIR)

    switch = []
    for t in types:
        name = t.split('Type')
        name = name[0].lower()

        classtype = t[0:-4] + '::class'

        switch.append((name, classtype))

    switch.sort(key=lambda x:x[0])

    for s in switch:
        print("case '{}': return \Symfony\Component\Form\Extension\Core\Type\{};".format(s[0], s[1]))

if __name__ == '__main__':
    main()
