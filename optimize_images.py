#!/usr/bin/env python3
"""
Otimizar imagens dos cursos para web
- Redimensionar para 1200x630px (16:9)
- Comprimir para ~150KB
- Manter qualidade visual
"""

from PIL import Image
import os

# Configurações
INPUT_DIR = "public/images/courses/image"
OUTPUT_DIR = "public/images/courses"
TARGET_SIZE = (1200, 630)
QUALITY = 85

# Mapeamento de arquivos
images_map = {
    "brasil.jpg": "curso_portugues_trabalho.jpg",
    "tiktok.jpg": "curso_conteudo_viral.jpg",
    "afiliado.jpg": "curso_marketing_afiliacao.jpg",
    "profission.jpg": "curso_monetizar_competencias.jpg",
}

def optimize_image(input_path, output_path, target_size=TARGET_SIZE, quality=QUALITY):
    """Otimiza uma imagem para web"""
    try:
        # Abrir imagem
        img = Image.open(input_path)
        
        # Converter para RGB se necessário
        if img.mode != 'RGB':
            img = img.convert('RGB')
        
        # Calcular dimensões mantendo proporção
        img.thumbnail((target_size[0] * 2, target_size[1] * 2), Image.LANCZOS)
        
        # Criar nova imagem com fundo branco
        new_img = Image.new('RGB', target_size, (255, 255, 255))
        
        # Centralizar imagem
        offset = ((target_size[0] - img.size[0]) // 2, (target_size[1] - img.size[1]) // 2)
        new_img.paste(img, offset)
        
        # Salvar com compressão
        new_img.save(output_path, 'JPEG', quality=quality, optimize=True)
        
        # Verificar tamanho
        size_kb = os.path.getsize(output_path) / 1024
        print(f"✅ {os.path.basename(output_path)}: {size_kb:.1f}KB")
        
        return True
    except Exception as e:
        print(f"❌ Erro ao processar {input_path}: {e}")
        return False

# Processar todas as imagens
print("🎨 Otimizando imagens dos cursos...\n")

success_count = 0
for input_file, output_file in images_map.items():
    input_path = os.path.join(INPUT_DIR, input_file)
    output_path = os.path.join(OUTPUT_DIR, output_file)
    
    if os.path.exists(input_path):
        if optimize_image(input_path, output_path):
            success_count += 1
    else:
        print(f"⚠️  Arquivo não encontrado: {input_file}")

print(f"\n✅ {success_count}/{len(images_map)} imagens otimizadas com sucesso!")
print(f"📁 Salvas em: {OUTPUT_DIR}/")
