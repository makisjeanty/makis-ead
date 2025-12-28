#!/usr/bin/env python3
from PIL import Image, ImageDraw, ImageFont
import os

# Create a 1200x630 placeholder image
width, height = 1200, 630
background_color = (107, 33, 168)  # Purple #6B21A8
text_color = (255, 255, 255)  # White

# Create image
img = Image.new('RGB', (width, height), background_color)
draw = ImageDraw.Draw(img)

# Add text
text = "Curso\nDisponível"
try:
    font = ImageFont.truetype("/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf", 80)
except:
    font = ImageFont.load_default()

# Get text bounding box
bbox = draw.textbbox((0, 0), text, font=font)
text_width = bbox[2] - bbox[0]
text_height = bbox[3] - bbox[1]

# Center text
x = (width - text_width) / 2
y = (height - text_height) / 2

# Draw text
draw.text((x, y), text, fill=text_color, font=font, align='center')

# Save
output_path = 'public/images/courses/placeholder.jpg'
os.makedirs(os.path.dirname(output_path), exist_ok=True)
img.save(output_path, 'JPEG', quality=85)
print(f"✅ Placeholder created: {output_path}")
