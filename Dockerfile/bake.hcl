group "default" {
	targets = ["nginx", "php"]
}

target "nginx" {
  context = "Dockerfile/nginx"
  dockerfile = "Dockerfile"
  tags = [
    "docker.pkg.github.com/fogospt/fogospt/nginx:fogospt-${GITHUB_SHA}"
    "docker.pkg.github.com/fogospt/fogospt/nginx:fogospt-latest"
    ]
}

target "php" {
  context = "Dockerfile/php"
  dockerfile = "Dockerfile"
  tags = [
    "docker.pkg.github.com/fogospt/fogospt/php:fogospt-${GITHUB_SHA}"
    "docker.pkg.github.com/fogospt/fogospt/php:fogospt-GITHUB_SHA:-latest"
  ]
  target = "php73"
}
