#!/usr/bin/env python3
import re

# Read the minified file
with open('resources/views/welcome.blade.php', 'r') as f:
    content = f.read()

# Remove duplicate "Tarifs" link (second occurrence in desktop menu)
# Pattern: find the second Tarifs link and replace with Contact
content = re.sub(
    r'(\<a href="/pricing" class="text-gray-700 hover:text-purple-600 font-medium transition"\>Tarifs\</a\>)(\s*\<a href="/pricing" class="text-gray-700 hover:text-purple-600 font-medium transition"\>Tarifs\</a\>)',
    r'\1 <a href="/contact" class="text-gray-700 hover:text-purple-600 font-medium transition">Contact</a>',
    content
)

# Write back
with open('resources/views/welcome.blade.php', 'w') as f:
    f.write(content)

print("✅ Menu corrigido: Tarifs duplicado removido, Contact adicionado")
