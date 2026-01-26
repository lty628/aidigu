/**
 * 特效管理系统
 * 包含多种主流特效：粒子、雪花、雨滴、星空等
 */

/**
 * 粒子特效类
 */
class Particle {
    constructor(x, y, config) {
        this.x = x;
        this.y = y;
        this.size = Math.random() * (config.maxSize - config.minSize) + config.minSize;
        this.speedX = Math.random() * (config.maxSpeed - config.minSpeed) + config.minSpeed;
        this.speedY = Math.random() * (config.maxSpeed - config.minSpeed) + config.minSpeed;
        const alpha = Math.random() * (config.maxAlpha - config.minAlpha) + config.minAlpha;
        const colorTemplate = config.colorPalette[Math.floor(Math.random() * config.colorPalette.length)];
        this.color = colorTemplate.replace('{alpha}', alpha);
    }

    update(width, height) {
        this.x += this.speedX;
        this.y += this.speedY;

        // 边界检测和反弹
        if (this.x <= 0 || this.x >= width) {
            this.speedX *= -1;
            this.x = Math.max(0, Math.min(width, this.x));
        }
        if (this.y <= 0 || this.y >= height) {
            this.speedY *= -1;
            this.y = Math.max(0, Math.min(height, this.y));
        }
    }

    draw(ctx) {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = this.color;
        ctx.fill();
    }
}

/**
 * 雪花特效类
 */
class Snowflake {
    constructor(width, height, config) {
        this.x = Math.random() * width;
        this.y = Math.random() * -height;
        this.size = Math.random() * (config.maxSize - config.minSize) + config.minSize;
        this.speed = Math.random() * (config.maxSpeed - config.minSpeed) + config.minSpeed;
        this.wind = (Math.random() - 0.5) * config.wind;
        this.color = config.color;
        this.oscillation = Math.random() * 0.02;
        this.angle = Math.random() * Math.PI * 2;
    }

    update(width, height) {
        this.y += this.speed;
        this.x += this.wind + Math.sin(this.angle) * 0.5;
        this.angle += this.oscillation;

        // 重置超出屏幕的雪花
        if (this.y > height) {
            this.y = -this.size;
            this.x = Math.random() * width;
        }
        if (this.x > width) {
            this.x = 0;
        } else if (this.x < 0) {
            this.x = width;
        }
    }

    draw(ctx) {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = this.color;
        ctx.fill();
    }
}

/**
 * 雨滴特效类
 */
class Raindrop {
    constructor(width, height, config) {
        this.x = Math.random() * width;
        this.y = Math.random() * -height;
        this.size = Math.random() * (config.maxSize - config.minSize) + config.minSize;
        this.speed = Math.random() * (config.maxSpeed - config.minSpeed) + config.minSpeed;
        this.wind = config.wind;
        this.color = config.color;
    }

    update(width, height) {
        this.x += this.wind;
        this.y += this.speed;

        // 重置超出屏幕的雨滴
        if (this.y > height) {
            this.y = -this.size * 3;
            this.x = Math.random() * width;
        }
        if (this.x > width) {
            this.x = 0;
        } else if (this.x < 0) {
            this.x = width;
        }
    }

    draw(ctx) {
        ctx.beginPath();
        ctx.moveTo(this.x, this.y);
        ctx.lineTo(this.x - this.size, this.y + this.size * 5);
        ctx.lineWidth = this.size / 2;
        ctx.strokeStyle = this.color;
        ctx.stroke();
    }
}

/**
 * 星星特效类
 */
class Star {
    constructor(width, height, config) {
        this.x = Math.random() * width;
        this.y = Math.random() * height;
        this.size = Math.random() * (config.maxSize - config.minSize) + config.minSize;
        this.baseAlpha = Math.random() * (config.maxAlpha - config.minAlpha) + config.minAlpha;
        this.alpha = this.baseAlpha;
        this.blinkSpeed = (Math.random() - 0.5) * config.blinkSpeed;
        this.color = config.color.replace('{alpha}', this.alpha);
    }

    update(config) {
        this.alpha += this.blinkSpeed;
        if (this.alpha > this.baseAlpha || this.alpha < config.minAlpha) {
            this.blinkSpeed *= -1;
        }
        this.color = config.color.replace('{alpha}', this.alpha);
    }

    draw(ctx) {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = this.color;
        ctx.fill();
    }
}

/**
 * 气泡特效类
 */
class Bubble {
    constructor(width, height, config) {
        this.x = Math.random() * width;
        this.y = height + Math.random() * 100;
        this.size = Math.random() * (config.maxSize - config.minSize) + config.minSize;
        this.speedX = (Math.random() - 0.5) * config.maxSpeed;
        this.floatSpeed = config.floatSpeed;
        this.baseAlpha = Math.random() * (config.maxAlpha - config.minAlpha) + config.minAlpha;
        this.alpha = this.baseAlpha;
        this.oscillation = Math.random() * 0.05;
        this.angle = Math.random() * Math.PI * 2;
        this.color = config.color.replace('{alpha}', this.alpha);
    }

    update(width, height) {
        this.x += this.speedX + Math.sin(this.angle) * 0.5;
        this.y += this.floatSpeed;
        this.angle += this.oscillation;

        // 重置超出屏幕的气泡
        if (this.y < -this.size) {
            this.y = height + this.size;
            this.x = Math.random() * width;
        }
    }

    draw(ctx) {
        // 绘制气泡主体
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = this.color;
        ctx.fill();

        // 绘制高光
        ctx.beginPath();
        ctx.arc(this.x - this.size / 3, this.y - this.size / 3, this.size / 3, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
        ctx.fill();
    }
}

class EffectsManager {
    constructor() {
        this.currentEffect = null;
        this.configs = {
            particles: {
                particleCount: 80,
                minSize: 1,
                maxSize: 5,
                minSpeed: -0.25,
                maxSpeed: 0.25,
                minAlpha: 0.3,
                maxAlpha: 0.8,
                colorPalette: [
                    'rgba(255, 107, 107, {alpha})', // 红色系
                    'rgba(107, 185, 255, {alpha})', // 蓝色系
                    'rgba(173, 107, 255, {alpha})', // 紫色系
                    'rgba(107, 255, 185, {alpha})'  // 青色系
                ],
                zIndex: 1,
                pointerEvents: 'none'
            },
            snow: {
                particleCount: 150,
                minSize: 2,
                maxSize: 8,
                minSpeed: 0.5,
                maxSpeed: 3,
                wind: 0.1,
                zIndex: 1,
                pointerEvents: 'none',
                color: 'rgba(255, 255, 255, 0.8)'
            },
            rain: {
                particleCount: 200,
                minSize: 1,
                maxSize: 2,
                minSpeed: 5,
                maxSpeed: 15,
                wind: 2,
                zIndex: 1,
                pointerEvents: 'none',
                color: 'rgba(135, 206, 250, 0.7)'
            },
            stars: {
                particleCount: 200,
                minSize: 0.5,
                maxSize: 2,
                blinkSpeed: 0.01,
                minAlpha: 0.2,
                maxAlpha: 1,
                zIndex: 1,
                pointerEvents: 'none',
                color: 'rgba(255, 255, 255, {alpha})'
            },
            bubbles: {
                particleCount: 60,
                minSize: 5,
                maxSize: 30,
                minSpeed: -1,
                maxSpeed: 1,
                floatSpeed: -0.5,
                minAlpha: 0.3,
                maxAlpha: 0.8,
                zIndex: 1,
                pointerEvents: 'none',
                color: 'rgba(173, 216, 230, {alpha})'
            }
        };
    }

    /**
     * 初始化指定特效
     */
    initEffect(effectType, containerSelector = '.background-blur') {
        // 如果已有特效运行，先清除
        if (this.currentEffect) {
            this.destroyCurrentEffect();
        }

        const config = this.configs[effectType];
        if (!config) {
            console.error(`特效类型 "${effectType}" 不存在`);
            return;
        }

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // 设置画布尺寸
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        // 设置画布样式
        Object.assign(canvas.style, {
            position: 'absolute',
            top: '0',
            left: '0',
            zIndex: config.zIndex.toString(),
            pointerEvents: config.pointerEvents
        });

        // 添加到指定容器
        const container = document.querySelector(containerSelector);
        if (container) {
            container.appendChild(canvas);
        } else {
            document.body.appendChild(canvas);
        }

        let particles = [];
        let animationId;

        // 根据特效类型创建粒子
        switch (effectType) {
            case 'particles':
                for (let i = 0; i < config.particleCount; i++) {
                    particles.push(new Particle(
                        Math.random() * canvas.width,
                        Math.random() * canvas.height,
                        config
                    ));
                }
                break;
            case 'snow':
                for (let i = 0; i < config.particleCount; i++) {
                    particles.push(new Snowflake(canvas.width, canvas.height, config));
                }
                break;
            case 'rain':
                for (let i = 0; i < config.particleCount; i++) {
                    particles.push(new Raindrop(canvas.width, canvas.height, config));
                }
                break;
            case 'stars':
                for (let i = 0; i < config.particleCount; i++) {
                    particles.push(new Star(canvas.width, canvas.height, config));
                }
                break;
            case 'bubbles':
                for (let i = 0; i < config.particleCount; i++) {
                    particles.push(new Bubble(canvas.width, canvas.height, config));
                }
                break;
        }

        // 动画循环
        const animate = () => {
            // 清除画布
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // 更新和绘制每个粒子
            for (const particle of particles) {
                if (effectType === 'stars') {
                    particle.update(config);
                } else {
                    particle.update(canvas.width, canvas.height);
                }
                particle.draw(ctx);
            }

            // 继续动画循环
            animationId = requestAnimationFrame(animate);
        };

        // 开始动画
        animate();

        // 窗口大小调整处理
        const handleResize = () => {
            if (!canvas) return;

            // 保存当前粒子位置比例
            const widthRatio = window.innerWidth / canvas.width;
            const heightRatio = window.innerHeight / canvas.height;

            // 更新画布尺寸
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            // 按比例调整粒子位置，保持相对位置
            for (const particle of particles) {
                particle.x *= widthRatio;
                particle.y *= heightRatio;
            }
        };

        // 添加事件监听器
        window.addEventListener('resize', handleResize);

        // 页面可见性变化时暂停/恢复动画
        const visibilityChangeHandler = () => {
            if (document.hidden) {
                if (animationId) {
                    cancelAnimationFrame(animationId);
                    animationId = null;
                }
            } else {
                if (!animationId) {
                    animate();
                }
            }
        };

        document.addEventListener('visibilitychange', visibilityChangeHandler);

        // 保存当前特效信息
        this.currentEffect = {
            type: effectType,
            canvas: canvas,
            particles: particles,
            animationId: animationId,
            resizeHandler: handleResize,
            visibilityHandler: visibilityChangeHandler
        };
    }

    /**
     * 销毁当前特效
     */
    destroyCurrentEffect() {
        if (!this.currentEffect) return;

        const { canvas, animationId, resizeHandler, visibilityHandler } = this.currentEffect;

        // 取消动画帧
        if (animationId) {
            cancelAnimationFrame(animationId);
        }

        // 移除事件监听器
        window.removeEventListener('resize', resizeHandler);
        document.removeEventListener('visibilitychange', visibilityChangeHandler);

        // 移除画布
        if (canvas && canvas.parentNode) {
            canvas.parentNode.removeChild(canvas);
        }

        this.currentEffect = null;
    }

    /**
     * 获取可用的特效类型
     */
    getAvailableEffects() {
        return Object.keys(this.configs);
    }

    /**
     * 切换到指定特效
     */
    switchEffect(effectType, containerSelector = '.background-blur') {
        this.initEffect(effectType, containerSelector);
    }
}

// // 创建全局实例
// const effectsManager = new EffectsManager();

// // 提供便捷函数
// function initParticles(containerSelector = '.background-blur') {
//     effectsManager.initEffect('particles', containerSelector);
// }

// function initSnow(containerSelector = '.background-blur') {
//     effectsManager.initEffect('snow', containerSelector);
// }

// function initRain(containerSelector = '.background-blur') {
//     effectsManager.initEffect('rain', containerSelector);
// }

// function initStars(containerSelector = '.background-blur') {
//     effectsManager.initEffect('stars', containerSelector);
// }

// function initBubbles(containerSelector = '.background-blur') {
//     effectsManager.initEffect('bubbles', containerSelector);
// }

// function switchEffect(effectType, containerSelector = '.background-blur') {
//     effectsManager.switchEffect(effectType, containerSelector);
// }

// function destroyCurrentEffect() {
//     effectsManager.destroyCurrentEffect();
// }

// // 在页面加载完成后初始化
// document.addEventListener('DOMContentLoaded', () => {
//     // 可以在这里设置默认特效
//     // initParticles(); // 默认初始化粒子特效
// });

// <script src="/static/index/js/particles_effects.js"></script>
// <script>
// // 创建全局实例
// const effectsManager = new EffectsManager();
// effectsManager.initEffect('particles', '.background-blur');
// // effectsManager.initEffect('snow', '.background-blur');
// // effectsManager.initEffect('rain', '.background-blur');
// // effectsManager.initEffect('stars', '.background-blur');
// // effectsManager.initEffect('bubbles', '.background-blur');


// </script>