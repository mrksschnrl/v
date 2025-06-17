const canvas = document.getElementById("logo-canvas");

const scene = new THREE.Scene();
scene.background = new THREE.Color("#000000"); // Schwarzer Hintergrund

const camera = new THREE.PerspectiveCamera(
  45,
  canvas.clientWidth / canvas.clientHeight,
  0.1,
  1000
);
camera.position.set(0, 0, 400); // weiter weg, damit Tiefe sichtbar ist

const renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true });
renderer.setSize(canvas.clientWidth, canvas.clientHeight);

// Licht
const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
directionalLight.position.set(0, 0, 300);
scene.add(directionalLight);
scene.add(new THREE.AmbientLight(0x555555));

// SVG laden und stark extrudieren
const loader = new THREE.SVGLoader();
loader.load("/v/includes/ci/logo.svg", (data) => {
  const group = new THREE.Group();

  data.paths.forEach((path) => {
    const shapes = THREE.SVGLoader.createShapes(path);
    shapes.forEach((shape) => {
      const geometry = new THREE.ExtrudeGeometry(shape, {
        depth: 50, // 🔴 starke Extrusion
        bevelEnabled: false,
      });

      const material = new THREE.MeshPhongMaterial({
        color: 0xff0000, // 🔴 ROT
        flatShading: true,
      });

      const mesh = new THREE.Mesh(geometry, material);
      group.add(mesh);
    });
  });

  group.scale.set(1, -1, 1); // normalisieren ohne Schrumpfung
  group.position.set(-50, -50, -25); // anpassen je nach SVG-Größe
  scene.add(group);
});

// Animation
function animate() {
  requestAnimationFrame(animate);
  scene.rotation.y += 0.01;
  renderer.render(scene, camera);
}
animate();

// Responsive
window.addEventListener("resize", () => {
  const w = canvas.clientWidth;
  const h = canvas.clientHeight;
  camera.aspect = w / h;
  camera.updateProjectionMatrix();
  renderer.setSize(w, h);
});
