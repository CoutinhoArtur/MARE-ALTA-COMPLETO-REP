<?php
// Inicia a sessão para verificar se o usuário está logado
session_start();
include('conexao.php');

// Busca produtos em destaque (limitado a 4)
// Agora busca a coluna 'imagem_full' ao invés de 'imagem' para melhor resolução
$produtos_destaque = $conn->query("
    SELECT p.*, f.nome AS marca_nome 
    FROM produtos p 
    JOIN fornecedores f ON p.fornecedor_id = f.id 
    LIMIT 4
");

// Busca marcas (limitado a 4)
// Agora busca a coluna 'imagem_full' ao invés de 'imagem' para melhor resolução
$marcas = $conn->query("SELECT * FROM fornecedores LIMIT 4");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportShop - A sua loja de esportes</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Header (Barra Marrom) -->
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <img src="./Imagens/icone_certo.png" alt="" style="width:170px;">
            </a>
            
            <!-- Links de Navegação (Desktop) -->
            <ul class="nav-links" id="nav-menu">
                <li><a href="#"><i class="fa-solid fa-tag"></i> <span>Promoções</span></a></li>
                <li><a href="#"><i class="fa-solid fa-shirt"></i> <span>Vestuário</span></a></li>
                <li><a href="#"><i class="fa-solid fa-gears"></i> <span>Acessórios</span></a></li>
                <li>
                    <?php
                    if (isset($_SESSION['usuario'])) {
                        echo '<a href="painel.php" class="login-link">
                                <i class="fa-solid fa-user-shield"></i>
                                <span>Painel</span>
                              </a>';
                    } else {
                        echo '<a href="login.php" class="login-link">
                                <i class="fa-solid fa-user"></i>
                                <span>Login</span>
                              </a>';
                    }
                    ?>
                </li>
            </ul>
            
            <!-- Botão Hamburguer (Mobile) -->
            <button class="menu-toggle" id="menu-toggle" aria-label="Abrir menu">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Barra de Busca -->
    <div class="search-bar">
        <div class="container">
            <form class="search-form">
                <input type="text" placeholder="O que você está procurando?">
                <button type="submit" aria-label="Buscar">
                    <i class="fa-solid fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Seção do Slider -->
    <section class="slider-section">
        <div class="slider-container">
            <!-- Slides -->
            <div class="slider">
                <div class="slide">
                    <img src="imagens/black fridau 1.png" alt="Black Friday">
                    <div class="slide-content">
                    </div>
                </div>
                <div class="slide">
                    <img src="imagens/medina 2(2).png" alt="Medina">
                    <div class="slide-content">
                    </div>
                </div>
                <div class="slide">
                    <img src="imagens/banner 3.png" alt="40% OFF">
                    <div class="slide-content">
                    </div>
                </div>
            </div>

            <!-- Botões de Navegação -->
            <div class="slider-nav">
                <button id="prev-slide" aria-label="Slide anterior"><i class="fa-solid fa-chevron-left"></i></button>
                <button id="next-slide" aria-label="Próximo slide"><i class="fa-solid fa-chevron-right"></i></button>
            </div>

            <!-- Pontos (Dots) -->
            <div class="slider-dots" id="slider-dots"></div>
        </div>
    </section>

    <!-- Barra de Informações -->
    <div class="info-bar">
        <div class="container">
            <div class="info-item">
                <i class="fa-solid fa-tag"></i>
                <div class="info-text">
                    <strong>10% OFF na 1ª compra</strong>
                    <span>Cadastre-se na Newsletter</span>
                </div>
            </div>
            <div class="info-item">
                <i class="fa-solid fa-credit-card"></i>
                <div class="info-text">
                    <strong>Até 10x sem juros</strong>
                    <span>Parcele em até 10x sem juros</span>
                </div>
            </div>
            <div class="info-item">
                <i class="fa-solid fa-store"></i>
                <div class="info-text">
                    <strong>Retire na Loja</strong>
                    <span>Compre online e retire na loja</span>
                </div>
            </div>
            <div class="info-item">
                <i class="fa-solid fa-truck-fast"></i>
                <div class="info-text">
                    <strong>Ganhe Entrega Grátis</strong>
                    <span>Frete grátis acima de R$ 299</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Destaques do Mês -->
    <section class="featured-section">
        <div class="container">
            <h2 class="section-title">Destaques do mês!</h2>
            <div class="products-grid">
                <?php if ($produtos_destaque && $produtos_destaque->num_rows > 0): ?>
                    <?php while ($produto = $produtos_destaque->fetch_assoc()): ?>
                        <div class="product-card">
                            <button class="wishlist-btn" aria-label="Adicionar aos favoritos">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                            <div class="product-image">
                                <?php 
                                // CORREÇÃO: Usa 'imagem_full' para melhor resolução no frontend
                                // Se 'imagem_full' não existir, usa 'imagem' como fallback
                                $imagem_produto = $produto['imagem_full'] ?? $produto['imagem'] ?? '';
                                
                                if ($imagem_produto): 
                                ?>
                                    <img src="<?php echo $imagem_produto; ?>" alt="<?php echo $produto['nome']; ?>">
                                <?php else: ?>
                                    <img src="https://placehold.co/300x400/cccccc/666666?text=Sem+Imagem" alt="Sem imagem">
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3><?php echo $produto['nome']; ?></h3>
                                <p class="product-brand"><?php echo $produto['marca_nome']; ?></p>
                                <p class="product-price">por R$<?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                                <button class="btn-add-cart">Ver Detalhes</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <!-- Produtos de exemplo caso não haja produtos no banco -->
                    <div class="product-card">
                        <button class="wishlist-btn"><i class="fa-regular fa-heart"></i></button>
                        <div class="product-image">
                            <img src="https://placehold.co/300x400/f4a460/ffffff?text=Prancha+Surf" alt="Prancha">
                        </div>
                        <div class="product-info">
                            <h3>Prancha de Surf 6'0</h3>
                            <p class="product-brand">Marca Exemplo</p>
                            <p class="product-price">por R$1.400,00</p>
                            <button class="btn-add-cart">Ver Detalhes</button>
                        </div>
                    </div>
                    <div class="product-card">
                        <button class="wishlist-btn"><i class="fa-regular fa-heart"></i></button>
                        <div class="product-image">
                            <img src="https://placehold.co/300x400/000000/ffffff?text=Roupa+Neoprene" alt="Roupa">
                        </div>
                        <div class="product-info">
                            <h3>Long John Wetsuit 3/2mm</h3>
                            <p class="product-brand">Marca Exemplo</p>
                            <p class="product-price">por R$568,77</p>
                            <button class="btn-add-cart">Ver Detalhes</button>
                        </div>
                    </div>
                    <div class="product-card">
                        <button class="wishlist-btn"><i class="fa-regular fa-heart"></i></button>
                        <div class="product-image">
                            <img src="https://placehold.co/300x400/ffffff/000000?text=Prancha+Stand+Up" alt="SUP">
                        </div>
                        <div class="product-info">
                            <h3>Prancha Stand Up Paddle</h3>
                            <p class="product-brand">Marca Exemplo</p>
                            <p class="product-price">por R$3.485,00</p>
                            <button class="btn-add-cart">Ver Detalhes</button>
                        </div>
                    </div>
                    <div class="product-card">
                        <button class="wishlist-btn"><i class="fa-regular fa-heart"></i></button>
                        <div class="product-image">
                            <img src="https://placehold.co/300x400/87ceeb/000000?text=Prancha+Bodyboard" alt="Bodyboard">
                        </div>
                        <div class="product-info">
                            <h3>Bodyboard 42"</h3>
                            <p class="product-brand">Marca Exemplo</p>
                            <p class="product-price">por R$485,00</p>
                            <button class="btn-add-cart">Ver Detalhes</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Marcas -->
    <section class="brands-section">
        <div class="container">
            <h2 class="section-title">Marcas</h2>
            <div class="brands-grid">
                <?php if ($marcas && $marcas->num_rows > 0): ?>
                    <?php while ($marca = $marcas->fetch_assoc()): ?>
                        <div class="brand-card">
                            <?php 
                            // CORREÇÃO: Usa 'imagem_full' para melhor resolução no frontend
                            // Se 'imagem_full' não existir, usa 'imagem' como fallback
                            $imagem_marca = $marca['imagem_full'] ?? $marca['imagem'] ?? '';
                            
                            if ($imagem_marca): 
                            ?>
                                <img src="<?php echo $imagem_marca; ?>" alt="<?php echo $marca['nome']; ?>">
                            <?php else: ?>
                                <div class="brand-placeholder"><?php echo substr($marca['nome'], 0, 1); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <!-- Marcas de exemplo -->
                    <div class="brand-card">
                        <img src="https://via.placeholder.com/150x80/ffffff/000000?text=HANG+LOOSE" alt="Hang Loose">
                    </div>
                    <div class="brand-card">
                        <img src="https://via.placeholder.com/150x80/ffffff/000000?text=RIP+CURL" alt="Rip Curl">
                    </div>
                    <div class="brand-card">
                        <img src="https://via.placeholder.com/150x80/ffffff/000000?text=QUIKSILVER" alt="Quiksilver">
                    </div>
                    <div class="brand-card">
                        <img src="https://via.placeholder.com/150x80/ffffff/000000?text=BILLABONG" alt="Billabong">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Notícias -->
    <section class="news-section">
        <div class="container">
            <h2 class="section-title">Notícias</h2>
            <div class="news-grid">
                <article class="news-card">
                    <div class="news-image">
                        <img src="https://chat.google.com/u/0/api/get_attachment_url?url_type=FIFE_URL&content_type=image%2Fpng&attachment_token=AOo0EEX8w0yiXsRIYEebMGcEP1Hv%2F0GjJ%2BiBfRaaLcGER52I18JiUuDYzJQnEw6lZ5MXWQVY%2B%2F69KXbxS6WEpRKfAHzPelCjoQI77A8KbC5qaMREOvDMFeCDNGu0EtoruYt8SUaKz5i%2BZ0qOmBWBtNBCDjblBNU6HE3N%2BIBBEP1D5dU3m8Oa97z5LkhE19cB3MjGRGH47fVHzP%2BSqJfwf0XmbPBbrVgn60IaZdFext1vIrSdpDqhXUwNgpN3S2hnmuf1tUvz96yyPlr1KW%2FEckbcHv6UMy4c8ZF8yi3wTAETAW9cHwqIpj9JHqq3RIIaTIcTGG3vbKoz9UDuMorpBQ0pDT8eT9%2BnJYahJxbQoDF%2BzxHiaCBVn%2Brup%2BlGpSZZob14aMq2i1XUPW4PUvCHFWMhD9Q6KMVgqcxirv3uD3vobAOUX%2Fxly661yhY3AGxGlPNDUSH84PBGbFnr9H5F904nqLL5I4gGCQ0u%2F7uZY0tDHD2ufsOeY4n6wJicj1VwbV1kU2OaJaMMNXutidjHa%2B2sVy9J8m1pQFlU1IDuGnSGf5%2FSlBxVJDu47tZMtuL4ipOtqsgGXT5H8JWnEd3O38s%3D&allow_caching=true&sz=w1920-h912-rw" alt="Notícia 1">
                    </div>
                    <div class="news-content">
                        <h3>Wesley Dantas é destaque na etapa de Maresias do Tour Paulista</h3>
                    </div>
                </article>
                <article class="news-card">
                    <div class="news-image">
                        <img src="https://chat.google.com/u/0/api/get_attachment_url?url_type=FIFE_URL&content_type=image%2Fpng&attachment_token=AOo0EEUFLeUu7Oxh8pXnoHZN11bEc7O79Oblw%2B28anvnFX%2F%2FP2HJEW1%2Bl89HDG%2F0ZepmNw8q0PFuc%2BUDy4T1RYGFMGTU6jKDiRE%2BPcS76UONcDTqNpFOHuc1ucjpP%2Fqre2giBFfqHame6oYvJh3bnnuGmfojAOTtoTRcJm1%2Br9YKyi1SDuSoyZH0es6OWGtjzf839GpBtmdM8jzv9myrrAANdK6VzI0SUEN8V1n4gPYK2SV67xjlxDh5rOuHTvpfHl5CTusZyQA93Cs%2BA3FAMSLtrMQVC6eb8Q3crW4ijnDsRfDKl5Cgz7YvY3YpJ30CZRYR27pvREhsVtc0mcOBYiiLKAKVB7p%2FoaTjlatj0N58ujYMTgxMVfggcCggXJl15H69GXWVP6vmZjUT2QlbLVH3ahhlBq4vYmthHWdEW2Xag1w15mSr1tRPiSXulWeYe7mq5%2BIy3gVt8AS60hiXbhlVrRFdt6J7lCEQ%2F2thoGfb5UwPhjdI%2B977YHihi6LgO3udvHP0l6%2Fhl4keOYJLUtIXhEVlrk6sIDjI6fxS%2FXw2zv07ySdHZ0NWhAdM2D1Sz6fWmgN%2Bo6c8OZhiWOF7ndzQuGs%2FZqw%3D&allow_caching=true&sz=w1920-h912-rw" width="400" height="300" alt="Notícia 2">
                    </div>
                    <div class="news-content">
                        <h3>Ator Klebber Toledo promove competição de surfe para crianças carentes na Praia Grande</h3>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter-section">
        <div class="container">
            <h2 class="section-title">Novidades e promoções</h2>
            <p class="newsletter-subtitle">Inscreva-se para receber novidades, promoções e descontos</p>
            <form class="newsletter-form">
                <div class="form-group">
                    <input type="text" placeholder="Nome" required>
                    <input type="email" placeholder="E-mail" required>
                    <button type="submit">ASSINAR NEWSLETTER</button>
                </div>
            </form>
        </div>
    </section>
    
    <!-- Rodapé -->
    <footer>
        <div class="container footer-content">
            <div class="footer-column">
                <h4><i class="fa-solid fa-water"></i> Surf Shop</h4>
                <p>Loja especializada em artigos esportivos para surf, stand up paddle e esportes aquáticos.</p>
            </div>
            <div class="footer-column">
                <h4>Links Rápidos</h4>
                <ul>
                    <li><a href="#">Início</a></li>
                    <li><a href="#">Produtos</a></li>
                    <li><a href="#">Marcas</a></li>
                    <li><a href="#">Contato</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Atendimento</h4>
                <ul>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Trocas e Devoluções</a></li>
                    <li><a href="#">Formas de Pagamento</a></li>
                    <li><a href="#">Frete e Entrega</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Contato</h4>
                <p><i class="fa-solid fa-location-dot"></i> Av. Exemplo, 123<br>São Paulo - SP</p>
                <p><i class="fa-solid fa-phone"></i> (11) 1234-5678</p>
                <p><i class="fa-solid fa-envelope"></i> contato@sportshop.com.br</p>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; 2025 SportShop. Todos os direitos reservados.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="scripts.js"></script>

</body>
</html>