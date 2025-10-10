import sys
import json
import cv2
import os
import numpy as np
from pathlib import Path


def process_image(filename, method):
    try:
        if not os.path.exists(filename):
            return {"status": "error", "error": f"File not found: {filename}"}

        img = cv2.imread(filename, cv2.IMREAD_GRAYSCALE)
        if img is None:
            return {"status": "error", "error": "Failed to read image"}

        file_path = Path(filename)
        out_file = str(file_path.parent / f"{file_path.stem}_result.png")

        if method == "Original":
            result = img
            features_text = "Original image (no processing)"
        elif method == "Median Filter":
            result = cv2.medianBlur(img, 5)
            features_text = "Applied Median Filter (kernel=5) for noise reduction"
        elif method == "Gaussian Filter":
            result = cv2.GaussianBlur(img, (5, 5), 0)
            features_text = "Applied Gaussian Filter (5x5) for smooth blurring"
        elif method == "Bilateral Filter":
            result = cv2.bilateralFilter(img, 9, 75, 75)
            features_text = "Applied Bilateral Filter - edge-preserving smoothing"
        elif method == "Histogram Equalization":
            result = cv2.equalizeHist(img)
            features_text = "Applied Histogram Equalization for contrast enhancement"
        elif method == "CLAHE":
            clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8, 8))
            result = clahe.apply(img)
            features_text = "Applied CLAHE (Contrast Limited Adaptive Histogram Equalization)"
        elif method == "Normalized":
            result = cv2.normalize(img, None, 0, 255, cv2.NORM_MINMAX)
            features_text = "Applied Normalization (0-255 range)"
        elif method == "Sobel X":
            result = cv2.Sobel(img, cv2.CV_64F, 1, 0, ksize=3)
            result = cv2.convertScaleAbs(result)
            features_text = "Applied Sobel X - horizontal edge detection"
        elif method == "Sobel Y":
            result = cv2.Sobel(img, cv2.CV_64F, 0, 1, ksize=3)
            result = cv2.convertScaleAbs(result)
            features_text = "Applied Sobel Y - vertical edge detection"
        elif method == "Laplacian":
            result = cv2.Laplacian(img, cv2.CV_64F)
            result = cv2.convertScaleAbs(result)
            features_text = "Applied Laplacian edge detection"
        elif method == "Canny":
            result = cv2.Canny(img, 100, 200)
            features_text = "Applied Canny edge detection (threshold: 100-200)"
        elif method == "Opening":
            kernel = np.ones((5, 5), np.uint8)
            result = cv2.morphologyEx(img, cv2.MORPH_OPEN, kernel)
            features_text = "Applied Morphological Opening (erosion + dilation)"
        elif method == "Closing":
            kernel = np.ones((5, 5), np.uint8)
            result = cv2.morphologyEx(img, cv2.MORPH_CLOSE, kernel)
            features_text = "Applied Morphological Closing (dilation + erosion)"
        elif method == "Erosion":
            kernel = np.ones((5, 5), np.uint8)
            result = cv2.erode(img, kernel, iterations=1)
            features_text = "Applied Erosion - shrinks bright regions"
        elif method == "Fourier Spectrum":
            dft = cv2.dft(np.float32(img), flags=cv2.DFT_COMPLEX_OUTPUT)
            dft_shift = np.fft.fftshift(dft)
            magnitude = 20 * np.log(cv2.magnitude(dft_shift[:, :, 0], dft_shift[:, :, 1]) + 1)
            result = cv2.normalize(magnitude, None, 0, 255, cv2.NORM_MINMAX, cv2.CV_8U)
            features_text = "Fourier Spectrum - frequency domain representation"
        elif method == "Global Threshold":
            _, result = cv2.threshold(img, 127, 255, cv2.THRESH_BINARY)
            features_text = "Applied Global Threshold (threshold=127)"
        elif method == "Adaptive Mean":
            result = cv2.adaptiveThreshold(img, 255, cv2.ADAPTIVE_THRESH_MEAN_C,
                                          cv2.THRESH_BINARY, 11, 2)
            features_text = "Applied Adaptive Mean Threshold"
        elif method == "Adaptive Gaussian":
            result = cv2.adaptiveThreshold(img, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C,
                                          cv2.THRESH_BINARY, 11, 2)
            features_text = "Applied Adaptive Gaussian Threshold"
        elif method == "Contour Detection":
            result = img.copy()
            _, binary = cv2.threshold(img, 127, 255, cv2.THRESH_BINARY)
            contours, _ = cv2.findContours(binary, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
            result = cv2.cvtColor(result, cv2.COLOR_GRAY2BGR)
            cv2.drawContours(result, contours, -1, (0, 255, 0), 2)
            features_text = f"Detected {len(contours)} contours"
        elif method == "GLCM Texture":
            mean_val = np.mean(img)
            std_val = np.std(img)
            result = img.copy()
            features_text = f"Texture Analysis:\nMean: {mean_val:.2f}\nStd Dev: {std_val:.2f}"
        elif method == "Shape Analysis":
            _, binary = cv2.threshold(img, 127, 255, cv2.THRESH_BINARY)
            contours, _ = cv2.findContours(binary, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
            result = cv2.cvtColor(img, cv2.COLOR_GRAY2BGR)
            if contours:
                cnt = max(contours, key=cv2.contourArea)
                area = cv2.contourArea(cnt)
                perimeter = cv2.arcLength(cnt, True)
                cv2.drawContours(result, [cnt], -1, (0, 255, 0), 2)
                features_text = f"Shape Analysis:\nArea: {area:.2f}\nPerimeter: {perimeter:.2f}"
            else:
                features_text = "No shapes detected"
        else:
            result = img
            features_text = "Unknown method - showing original"

        cv2.imwrite(out_file, result)

        return {
            "status": "ok",
            "result_file": out_file,
            "features_text": features_text
        }
    except Exception as e:
        return {"status": "error", "error": f"Processing error: {str(e)}"}


def main():
    if len(sys.argv) < 3:
        print(json.dumps({
            "status": "error",
            "error": "Usage: python process.py <file> <method>"
        }))
        sys.exit(1)

    filename = sys.argv[1]
    method = sys.argv[2]

    result = process_image(filename, method)
    print(json.dumps(result))


if __name__ == "__main__":
    main()


