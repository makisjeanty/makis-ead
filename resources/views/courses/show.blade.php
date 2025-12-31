<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>{{ $course->title }} - Makis EAD</title>
    <link rel="icon" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            background: #F8FAFC;
        }

        .header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #F59E0B, #8B5CF6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .hero {
            background: linear-gradient(135deg, #F59E0B, #8B5CF6);
            color: white;
            padding: 4rem 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .content {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            margin: 2rem auto;
            max-width: 1200px;
        }

        .price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #F59E0B;
            margin: 2rem 0;
        }

        .btn {
            background: linear-gradient(135deg, #F59E0B, #D97706);
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
        <a href="/" class="logo">Makis EAD</a>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="{{ route('courses.index') }}"
                style="text-decoration: none; color: #4B5563; font-weight: 600;">Cursos</a>
            @auth
                <a href="{{ route('student.dashboard') }}"
                    style="text-decoration: none; color: #8B5CF6; font-weight: 600;">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit"
                        style="background: none; border: none; color: #EF4444; font-weight: 600; cursor: pointer; font-family: inherit; font-size: 1rem;">Sair</button>
                </form>
            @else
                <a href="{{ route('login') }}" style="text-decoration: none; color: #4B5563; font-weight: 600;">Entrar</a>
                <a href="{{ route('register') }}"
                    style="background: #8B5CF6; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600;">Criar
                    Conta</a>
            @endauth
        </div>
    </div>

    <div class="hero">
        <div class="container">
            <div style="display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
                @if($course->image)
                    <div style="flex: 0 0 300px; max-width: 100%;">
                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" style="width: 100%; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    </div>
                @endif
                <div style="flex: 1;">
                    <h1 style="font-size: 3rem; margin-bottom: 1rem;">{{ $course->title }}</h1>
                    <p style="font-size: 1.25rem;">{{ $course->description }}</p>
                    <div style="margin-top: 1.5rem;">
                        ⭐ {{ $course->rating }} • {{ $course->students_count }} alunos • {{ $course->duration_hours }}h
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <h2 style="margin-bottom: 1rem;">Sobre o Curso</h2>
        <p style="line-height: 1.8; color: #64748B; margin-bottom: 2rem;">
            {{ $course->long_description ?? $course->description }}
        </p>

        <h2 style="margin-bottom: 1rem;">Instrutor</h2>
        <p style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $course->instructor_name }}</p>
        <p style="color: #64748B;">{{ $course->instructor_bio ?? "Instrutor especializado" }}</p>

        @auth
            @if(auth()->user()->enrollments->contains('course_id', $course->id))
                <a href="{{ route('student.classroom.watch', $course->slug) }}" class="btn" style="background-color: #8B5CF6; border-color: #8B5CF6;">
                    Acessar Curso
                </a>
            @elseif($course->price == 0)
                <div class="price" style="color: #10B981;">Gratuito</div>
                <a href="{{ route('cart.add', $course->id) }}" class="btn" style="background-color: #10B981; border-color: #10B981;">
                    Matricular-se Agora
                </a>
            @else
                <div class="price">R$ {{ number_format($course->price, 2, ",", ".") }}</div>
                <a href="{{ route('cart.add', $course->id) }}" class="btn">Adicionar ao Carrinho</a>
            @endif
        @else
            @if($course->price == 0)
                <div class="price" style="color: #10B981;">Gratuito</div>
                <a href="{{ route('cart.add', $course->id) }}" class="btn" style="background-color: #10B981; border-color: #10B981;">
                    Matricular-se Agora (Login Necessário)
                </a>
            @else
                <div class="price">R$ {{ number_format($course->price, 2, ",", ".") }}</div>
                <a href="{{ route('login') }}" class="btn">Fazer Login para Comprar</a>
            @endif
        @endauth
    </div>
</body>

</html>
