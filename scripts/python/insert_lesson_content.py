#!/usr/bin/env python3
"""
Script to insert Google AdSense course and update all lesson content
with full Mimo.org format text from artifacts.
"""

import mysql.connector
from mysql.connector import Error

# Database configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'etude_user',
    'password': 'etude_pass_2025',
    'database': 'etude_rapide'
}

def get_connection():
    """Create database connection"""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        return conn
    except Error as e:
        print(f"❌ Error connecting to database: {e}")
        return None

def insert_adsense_course(conn):
    """Insert Google AdSense course"""
    cursor = conn.cursor()
    
    # First, get the category ID for "Ganhar Dinheiro Online"
    cursor.execute("SELECT id FROM categories WHERE name LIKE '%Dinheiro%' OR name LIKE '%Money%' LIMIT 1")
    result = cursor.fetchone()
    category_id = result[0] if result else 1
    
    # Insert course
    course_query = """
    INSERT INTO courses (
        category_id, title, slug, description, long_description,
        is_published, price, price_tier, level, duration_hours,
        instructor_name, rating, students_count, category
    ) VALUES (
        %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
    )
    """
    
    course_data = (
        category_id,
        'Google AdSense: Blog et Revenus',
        'google-adsense-blog-revenus',
        'Créez un blog rentable avec Google AdSense sans apparaître à la caméra. 30 leçons pratiques en français.',
        'Apprenez à créer un blog WordPress, produire du contenu optimisé SEO, et générer des revenus passifs avec Google AdSense. Parfait pour ceux qui ne veulent pas apparaître devant la caméra. Formation complète de A à Z en français.',
        1,  # is_published
        29.90,  # price
        'pratico',  # price_tier
        'Iniciante',  # level
        15,  # duration_hours
        'Makis EAD',  # instructor_name
        5.0,  # rating
        0,  # students_count
        'Ganhar Dinheiro Online'  # category
    )
    
    try:
        cursor.execute(course_query, course_data)
        conn.commit()
        course_id = cursor.lastrowid
        print(f"✅ Course inserted with ID: {course_id}")
        return course_id
    except Error as e:
        print(f"❌ Error inserting course: {e}")
        return None
    finally:
        cursor.close()

def insert_adsense_modules(conn, course_id):
    """Insert modules for AdSense course"""
    cursor = conn.cursor()
    
    modules = [
        ('Introduction et Fondations', 'Comprendre Google AdSense et créer votre blog', 1),
        ('Création de Contenu', 'Produire du contenu qui attire et convertit', 2),
        ('Optimisation et Monétisation', 'Maximiser vos revenus AdSense', 3),
    ]
    
    module_query = """
    INSERT INTO modules (course_id, title, description, sort_order)
    VALUES (%s, %s, %s, %s)
    """
    
    module_ids = []
    for title, description, sort_order in modules:
        try:
            cursor.execute(module_query, (course_id, title, description, sort_order))
            conn.commit()
            module_ids.append(cursor.lastrowid)
            print(f"✅ Module inserted: {title}")
        except Error as e:
            print(f"❌ Error inserting module {title}: {e}")
    
    cursor.close()
    return module_ids

def check_course_exists(conn, title_pattern):
    """Check if course exists"""
    cursor = conn.cursor()
    cursor.execute("SELECT id FROM courses WHERE title LIKE %s", (f'%{title_pattern}%',))
    result = cursor.fetchone()
    cursor.close()
    return result[0] if result else None

def main():
    """Main execution"""
    print("=" * 60)
    print("INSERTING GOOGLE ADSENSE COURSE")
    print("=" * 60)
    
    conn = get_connection()
    if not conn:
        return
    
    try:
        # Check if course already exists
        existing_id = check_course_exists(conn, 'AdSense')
        
        if existing_id:
            print(f"⚠️  Course already exists with ID: {existing_id}")
            print("Skipping course insertion...")
            course_id = existing_id
        else:
            # Insert course
            course_id = insert_adsense_course(conn)
            if not course_id:
                print("❌ Failed to insert course")
                return
            
            # Insert modules
            module_ids = insert_adsense_modules(conn, course_id)
            print(f"\n✅ Inserted {len(module_ids)} modules")
        
        print("\n" + "=" * 60)
        print("NEXT STEP: Update lesson content")
        print("=" * 60)
        print("\nRun the update_lesson_content.py script to insert")
        print("the full Mimo.org content for all 77 lessons.")
        
    finally:
        conn.close()

if __name__ == '__main__':
    main()
