// 添加图片压缩函数
function compressImage(file, callback) {
    // 获取文件大小（MB）
    const fileSize = file.size / (1024 * 1024);
    
    // 根据文件大小设定目标压缩大小（KB）
    let targetSize;
    if (fileSize <= 1) {
        targetSize = 100; // 100KB
    } else if (fileSize <= 2) {
        targetSize = 150; // 150KB
    } else if (fileSize <= 3) {
        targetSize = 200; // 200KB
    } else {
        targetSize = 250; // 250KB
    }
    
    // 创建 FileReader 对象
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.src = e.target.result;
        img.onload = function() {
            // 创建 canvas 元素
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            // 设置 canvas 尺寸
            const maxWidth = 1920;
            const maxHeight = 1080;
            let width = img.width;
            let height = img.height;
            
            // 按比例缩放尺寸
            if (width > height) {
                if (width > maxWidth) {
                    height = (height * maxWidth) / width;
                    width = maxWidth;
                }
            } else {
                if (height > maxHeight) {
                    width = (width * maxHeight) / height;
                    height = maxHeight;
                }
            }
            
            canvas.width = width;
            canvas.height = height;
            
            // 在 canvas 上绘制图片
            ctx.drawImage(img, 0, 0, width, height);
            
            // 初始压缩质量
            let quality = 0.92;
            let dataUrl = canvas.toDataURL('image/jpeg', quality);
            
            // 循环压缩直到达到目标大小
            while (dataUrl.length / 1024 > targetSize && quality > 0.1) {
                quality -= 0.1;
                dataUrl = canvas.toDataURL('image/jpeg', quality);
            }
            
            // 如果还是太大，则调整尺寸
            while (dataUrl.length / 1024 > targetSize && (width > 100 || height > 100)) {
                width *= 0.9;
                height *= 0.9;
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);
                dataUrl = canvas.toDataURL('image/jpeg', quality);
            }
            
            // 转换为 Blob 对象
            const blob = dataURItoBlob(dataUrl);
            const compressedFile = new File([blob], file.name, { type: 'image/jpeg' });
            
            callback(compressedFile);
        };
    };
    reader.readAsDataURL(file);
}

// 将 DataURL 转换为 Blob
function dataURItoBlob(dataURI) {
    let byteString;
    if (dataURI.split(',')[0].indexOf('base64') >= 0) {
        byteString = atob(dataURI.split(',')[1]);
    } else {
        byteString = unescape(dataURI.split(',')[1]);
    }
    
    const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
    const ia = new Uint8Array(byteString.length);
    for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }
    
    return new Blob([ia], { type: mimeString });
}
