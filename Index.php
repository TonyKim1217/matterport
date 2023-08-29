<?php 
include_once('./config.php');
?>
<html>
  <head>
    <script src='//static.matterport.com/showcase-sdk/latest.js'></script>
  </head>
  <body>
    <iframe
      width="100%"
      height="480"
      src="//my.matterport.com/show?m=<?=MODEL_ID?>&play=1&applicationKey=<?=MATTERPORT_KEY?>"
      frameborder="0"
      allowfullscreen
      allow="xr-spatial-tracking"
      id="showcase-iframe">
    </iframe>
    <script>

      (async function connectSdk() {
        const sdkKey = '<?=MATTERPORT_KEY?>'; // TODO: replace with your sdk key
        const iframe = document.getElementById('showcase-iframe');

        // connect the sdk; log an error and stop if there were any connection issues
        try {
          const mpSdk = await window.MP_SDK.connect(
            iframe, // Obtained earlier
            sdkKey, // Your SDK key
            '' // Unused but needs to be a valid string
          );
          onShowcaseConnect(mpSdk);
        } catch (e) {
          console.error(e);
        }
      })();

      var mattertags = [{
        label: 'Custom Tag 1',
        description: '[Link to Google!](https://www.google.com)',
        anchorPosition: { x: 0, y: 0, z: 0},
        stemVector: { x: 1, y: 1, z: 0} //태그 줄기 길이
      },{
        label: 'Custom Tag 2',
        description: 'This Tag included Photo resouces',
        anchorPosition: { x: 1, y: 0, z: 0 },
        stemVector: { x: 0, y: 1, z: 1} //태그 줄기 길이
      },{
        label: 'Custom Tag 3',
        description: 'This Tag included Media resouces',
        anchorPosition: { x: 2, y: 0, z: 0 },
        stemVector: { x: 0, y: 1, z: 0} //태그 줄기 길이
      }];

      


      async function onShowcaseConnect(mpSdk) {
        // insert your sdk code here. See the ref https://matterport.github.io/showcase-sdk//docs/reference/current/index.html
        
        
        // 1. 앵커태그 추가 (https://matterport.github.io/showcase-sdk/sdk_creating_mattertags.html)
        mpSdk.Mattertag.add(mattertags).then(function(mattertagIds) {
          // 2. 메타태그 Custom Tag 2에 이미지 리소스 추가하기
          addMedia(mpSdk, mattertagIds[1],"PHOTO",'https://static.matterport.com/mp_cms/media/filer_public/71/ca/71cabc75-99a5-41a4-917c-f45203f9254e/pro-21f8ddbae.png');
          // 3. 메타태그 Custom Tag 3에 영상 리소스 추가하기
          addMedia(mpSdk, mattertagIds[2],"VIDEO",'https://www.youtube.com/watch?v=wLIzWD1gru8');
          // 4. 마우스 포지션 획득 
          mpSdk.Pointer.intersection.subscribe(function (intersectionData) {
            // Changes to the intersection data have occurred.
            console.log('Intersection position:', intersectionData.position);
          });
          // 5. 장면에 오브젝트 모델 삽입하기 (번들용 SDK 부터 Scene오브젝트 사용 가능함으로 테스트 불가)
          // addModel(mpSdk);
          
        });

        try {
          const modelData = await mpSdk.Model.getData();
          console.log('Model sid:' + modelData.sid);
        } catch (e) {
          console.error(e);
        }
      }

      async function addMedia(sdk, tagId, type_val, src_val){
        sdk.Mattertag.editBillboard(tagId, {
            media: {
            type: type_val == "PHOTO" ? sdk.Mattertag.MediaType.PHOTO : sdk.Mattertag.MediaType.VIDEO,
            src: src_val,
          }
        });
      }

      async function addModel(sdk){
        // 오브젝트 모델을 제대로 보여주기 위해 레이어(장면) 및 조명 추가
        const [ sceneObject ] = sdk.Scene.createObjects(1);
        const lights = sceneObject.addNode();
        lights.addComponent('mp.lights');
        lights.start();
          
        //장면 노드에 모델 추가
        const modelNode = sceneObject.addNode();
        // Load Type : gltf바이너리[GLTF_LOADER], obj[OBJ_LOADER], dae[DAE_LOADER], fbx[FBX_LOADER]
        const fbxComponent = modelNode.addComponent(sdk.Scene.Component.FBX_LOADER, {
          url: 'https://cdn.jsdelivr.net/gh/mrdoob/three.js@dev/examples/models/fbx/stanford-bunny.fbx',
        });
        // 모델 크기 조정
        fbxComponent.inputs.localScale = {
          x: 0.00002,
          y: 0.00002,
          z: 0.00002
        };
        // 모델 배치
        modelNode.obj3D.position.set(0,-1,0); // drop ~3 feet
        // 
        modelNode.start();
      }
    </script>
  <body>
</html>