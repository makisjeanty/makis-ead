#!/usr/bin/env python3
"""
Script para inserir conteúdo das 11 lições do curso gratuito no banco de dados
"""

import mysql.connector
import sys

# Configuração do banco
config = {
    'user': 'etude_user',
    'password': 'etude_pass_2025',
    'host': '127.0.0.1',
    'database': 'etude_rapide'
}

# Conteúdo das lições (resumido para o script - conteúdo completo está no arquivo curso_gratuito_completo.md)
lessons_content = {
    157: {
        'title': 'Por Que É Possível Ganhar Online Sem Diploma',
        'content': '''# Por Que É Possível Ganhar Online Sem Diploma

## 🌍 A Nova Realidade

A internet mudou tudo. Hoje, você não precisa de diploma para ganhar dinheiro online. O que importa são suas habilidades, dedicação e vontade de aprender.

## 📊 Números Que Provam

- **73% dos freelancers** online não têm diploma universitário
- **Milhões de pessoas** ganham R$ 2.000-10.000/mês sem formação
- **Empresas contratam** por habilidade, não por diploma

## 💡 Por Que Funciona?

**1. Internet democratizou o acesso**
- Qualquer pessoa pode criar conteúdo
- Plataformas gratuitas disponíveis
- Clientes no mundo todo

**2. Habilidades práticas valem mais**
- Saber fazer > ter diploma
- Resultados importam mais que certificados
- Portfólio substitui currículo

**3. Barreiras foram eliminadas**
- Não precisa de escritório
- Investimento inicial baixo
- Pode começar hoje mesmo

## ✅ O Que Você Precisa

- ✅ Acesso à internet
- ✅ Vontade de aprender
- ✅ Dedicação de 2-3 horas/dia
- ✅ Paciência para crescer

## 🎯 Próxima Lição

Vamos conhecer as 5 formas comprovadas de ganhar dinheiro online que milhares de pessoas já usam com sucesso.

---
⏱️ Tempo: 5 minutos | 📊 Progresso: 1/11'''
    },
    158: {
        'title': 'Mentalidade Certa para Começar',
        'content': '''# Mentalidade Certa para Começar

## 🧠 Mindset de Sucesso

Ganhar dinheiro online não é sorte. É resultado de mentalidade correta + ação consistente.

## ❌ Mitos que Você Deve Esquecer

### Mito 1: "Preciso de muito dinheiro para começar"
**Realidade:** 4 das 5 formas custam R$ 0 para começar

### Mito 2: "Vou ficar rico rápido"
**Realidade:** Primeiros R$ 100 em 2-4 semanas. Crescimento gradual.

### Mito 3: "Preciso saber tudo antes de começar"
**Realidade:** Você aprende fazendo. Comece imperfeito.

### Mito 4: "Não tenho habilidades"
**Realidade:** Todo mundo tem algo para oferecer.

## ✅ Mentalidade Vencedora

### 1. Pense em Progresso, Não Perfeição
- Primeira venda > curso perfeito
- Feito > perfeito
- Melhore enquanto faz

### 2. Seja Paciente e Consistente
- Primeiros 30 dias: aprendizado
- 60-90 dias: primeiras vendas
- 6 meses: renda estável

### 3. Trate Como Negócio
- Dedique 2-3 horas/dia
- Tenha horário fixo
- Acompanhe resultados

### 4. Aprenda Com Erros
- Todo erro é aprendizado
- Ajuste e continue
- Não desista no primeiro "não"

## 🎯 Regra de Ouro

> "Ação imperfeita hoje vale mais que plano perfeito amanhã"

---
⏱️ Tempo: 6 minutos | 📊 Progresso: 3/11'''
    }
}

try:
    # Conectar ao banco
    conn = mysql.connector.connect(**config)
    cursor = conn.cursor()
    
    print("🎓 Inserindo conteúdo das lições...\n")
    
    # Atualizar cada lição
    for lesson_id, data in lessons_content.items():
        cursor.execute(
            "UPDATE lessons SET content = %s WHERE id = %s",
            (data['content'], lesson_id)
        )
        print(f"✅ Lição {lesson_id}: {data['title']}")
    
    # Commit
    conn.commit()
    
    print(f"\n✅ {len(lessons_content)} lições atualizadas com sucesso!")
    print("📁 Conteúdo completo em: curso_gratuito_completo.md")
    
    cursor.close()
    conn.close()
    
except mysql.connector.Error as err:
    print(f"❌ Erro: {err}")
    sys.exit(1)
