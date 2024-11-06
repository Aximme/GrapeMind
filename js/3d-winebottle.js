import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { OBJLoader } from 'three/addons/loaders/OBJLoader.js';
import { MTLLoader } from 'three/addons/loaders/MTLLoader.js';

const container = document.getElementById('scene-container');
const scene = new THREE.Scene();
scene.background = new THREE.Color(0xffffff);

const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
// -30 baisse encore plus la caméra
camera.position.set(0, -20, 5);

const renderer = new THREE.WebGLRenderer({ antialias: true });
renderer.setSize(container.clientWidth, container.clientHeight);
container.appendChild(renderer.domElement);

const controls = new OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;
controls.dampingFactor = 0.05;

// On desactive le zoom de l'utilsateur pour l'essayer que l'event du mouvement de souris.
controls.enableZoom = false;
controls.enableRotate = false;
controls.enablePan = false;

const ambientLight = new THREE.AmbientLight(0xffffff, 2);
scene.add(ambientLight);

const pivot = new THREE.Group();
scene.add(pivot);

/*
// Rend le pivot visible
const axesHelper = new THREE.AxesHelper(5); // 5 = longueur axes pivot
pivot.add(axesHelper);

const gridHelper = new THREE.GridHelper(10, 10); // 10 est la taille et 10 est la division de la grille
pivot.add(gridHelper);
*/

let bottle;
const mtlLoader = new MTLLoader();
const objLoader = new OBJLoader();

const rotationSpeed = 0.05; // Ajuste cette valeur pour contrôler la douceur du mouvement
const targetRotation = { x: -2, y: 0.05 }; // Initialiser targetRotation à la position de départ
let initialRotation = { x: -2, y: 0.05 }; // Position initiale de la bouteille

// Mettre à jour targetRotation en fonction de la souris
window.addEventListener('mousemove', (event) => {
    targetRotation.x = initialRotation.x + (event.clientY / window.innerHeight - 0.5) * Math.PI * 0.1;
    targetRotation.y = initialRotation.y + (event.clientX / window.innerWidth - 0.5) * Math.PI * 0.1;
});

mtlLoader.load(
    'assets/3d_model/750_mL_Wine_Bottle_v2/14042_750_mL_Wine_Bottle_r_v2_L3.mtl',
    function(materials) {
        materials.preload();
        objLoader.setMaterials(materials);

        objLoader.load(
            'assets/3d_model/750_mL_Wine_Bottle_v2/14042_750_mL_Wine_Bottle_r_v2_L3.obj',
            function(object) {
                const box = new THREE.Box3().setFromObject(object);
                const center = box.getCenter(new THREE.Vector3());
                const size = box.getSize(new THREE.Vector3());

                // Position de la bouteille au spawn
                object.position.set(0, -14, 0); // Position X: 0, Position Y: -0.2, Position Z: 0
                pivot.position.set(0, -14, 0);

                // Rotation initiale
                object.rotation.set(initialRotation.x, initialRotation.y, 0); // Rotation initiale pour garder la position correcte
                pivot.add(object);
                bottle = object;

                camera.fov = 40;
                camera.updateProjectionMatrix();

                const maxDim = Math.max(size.x, size.y, size.z);
                const fov = camera.fov * (Math.PI / 180);
                let cameraDistance = Math.abs(maxDim / 2 / Math.tan(fov / 2)) * 0.8;

                camera.position.z = cameraDistance;
                camera.position.set(20, 50, 70); // camera position

                document.getElementById('loading').style.display = 'none';

                // MENU INTERFACE POUR BOUGER LA BOUTEILLE
                /*
                const gui = new dat.GUI();
                const bottleFolder = gui.addFolder('Bouteille');
                bottleFolder.add(bottle.position, 'x', -100, 20).name('Position X');
                bottleFolder.add(bottle.position, 'y', -100, 20).name('Position Y');
                bottleFolder.add(bottle.position, 'z', -100, 20).name('Position Z');
                bottleFolder.add(bottle.rotation, 'x', -Math.PI, Math.PI).name('Rotation X').step(0.01);
                bottleFolder.add(bottle.rotation, 'y', -Math.PI, Math.PI).name('Rotation Y').step(0.01);
                bottleFolder.add(bottle.rotation, 'z', -Math.PI, Math.PI).name('Rotation Z').step(0.01);
                bottleFolder.open();

                const pivotFolder = gui.addFolder('Pivot');
                pivotFolder.add(pivot.position, 'x', -100, 100).name('Position X');
                pivotFolder.add(pivot.position, 'y', -100, 100).name('Position Y');
                pivotFolder.add(pivot.position, 'z', -100, 100).name('Position Z');
                pivotFolder.open();
                */
            }
        );
    }
);

function animate() {
    requestAnimationFrame(animate);
    if (bottle) {
        // Applique le mouvement de rotation de manière douce vers la cible
        bottle.rotation.x += (targetRotation.x - bottle.rotation.x) * rotationSpeed;
        bottle.rotation.y += (targetRotation.y - bottle.rotation.y) * rotationSpeed;
    }
    controls.update();
    renderer.render(scene, camera);
}
animate();

window.addEventListener('resize', onWindowResize, false);
function onWindowResize() {
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
}