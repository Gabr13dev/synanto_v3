<style>
    .h-30-rem {
        height: 30rem;
    }

    .img-slide {
        display: none;
    }

    .ativo {
        display: block !important;
    }

    @media(max-width: 720px) {
        .h-30-rem {
            height: 18rem;
        }
    }

    p.MsoNormal {
        padding-top: 3px;
        padding-bottom: 3px;
    }
</style>
<div class="site-section main-view">
    <div class="w-full text-center my-16">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white">
                <main class="my-8">
                    <div class="container mx-auto px-6">
                        <!-- SLIDE -->
                        <div id="slide" class="px-4 py-6 sm:px-0">
                            <?php foreach ($images as $key => $slide) { ?>
                                <div id="image<?= $key ?>" class="rounded-3xl w-full h-30-rem bg-cover bg-no-repeat fix-center img-slide" style="background-image: url('<?= $slide["img_slide"] ?>');"></div>
                            <?php } ?>
                        </div>
                        <div id="dotlist" class="flex justify-between w-12 mx-auto pb-2">
                            <?php foreach ($images as $key => $dot) { ?>
                                <button id="dot<?= $key ?>" class="dot bg-green-<?= $key != 0 ? '400' : '800' ?> rounded-full w-4 p-2 mr-1"></button>
                            <?php } ?>
                        </div>
                        <!--ULTIMA NOTICIA-->
                        <div class="h-96 rounded-md overflow-hidden bg-cover bg-center mt-4" style="background-image: url('https://sistemas.jhcgadvocacia.com.br/midia/noticias/<?= $post['capa_post'] ?>')">
                            <div class="bg-gray-900 bg-opacity-50 flex items-center h-full">
                                <div class="px-10 max-w-xl">
                                    <h2 class="text-2xl text-white font-semibold text-left"><?= $post['titulo_post'] ?></h2>
                                    <p class="mt-2 text-white text-left"><?= $post['resumo_post'] ?></p>
                                    <button onclick="window.location='<?= URL ?>/Post/<?= $post['id_post'] ?>'" class="flex items-center mt-4 px-3 py-2 bg-green-500 text-white text-sm uppercase font-medium rounded hover:bg-green-600 focus:outline-none focus:bg-blue-500">
                                        <span>Ver notícia <i class="fa fa-newspaper-o h-5 w-5 "></i></span>

                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="md:flex mt-8 md:-mx-4">
                            <!--Vagas Internas-->
                            <div class="w-full h-64 md:mx-4 rounded-md overflow-hidden bg-cover bg-center md:w-1/2 div-hire" style="background-image: url('https://sistemas.jhcgadvocacia.com.br/midia/roster/mario-gogh-VBLHICVh-lI-unsplash.jpg')" id="div-hire">
                                <div class="bg-gray-900 bg-opacity-50 flex items-center h-full" id="cover-hire">
                                    <div class="px-10 max-w-xl roster" id="roster-hire">
                                        <h2 class="text-2xl text-white font-semibold">Vagas Internas</h2>
                                        <button id="btn-hire" class="flex items-center mt-4 px-3 py-2 bg-green-500 text-white text-sm uppercase font-medium rounded hover:bg-green-600 focus:outline-none focus:bg-blue-500">
                                            <span>Ver Vagas</span>
                                            <svg class="h-5 w-5 mx-2" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="briefcase" class="svg-inline--fa fa-briefcase fa-w-16" role="img" viewBox="0 0 512 512">
                                                <path fill="currentColor" d="M320 336c0 8.84-7.16 16-16 16h-96c-8.84 0-16-7.16-16-16v-48H0v144c0 25.6 22.4 48 48 48h416c25.6 0 48-22.4 48-48V288H320v48zm144-208h-80V80c0-25.6-22.4-48-48-48H176c-25.6 0-48 22.4-48 48v48H48c-25.6 0-48 22.4-48 48v80h512v-80c0-25.6-22.4-48-48-48zm-144 0H192V96h128v32z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="max-w-xl list m-auto hidden overflow-y-auto" id="list-hire">
                                        <?php if (count($newHire) != 0) {
                                            foreach ($newHire as $listHire) { ?>
                                                <div class="bg-white w-full flex items-center p-2 rounded-xl shadow border my-8">
                                                    <div class="relative flex items-center space-x-4">
                                                        <?= $ctl->getProfilePicture($listHire['idgestor_vagas'] ,"rounded-full shadow-xl mx-auto h-12 w-12") ?>
                                                    </div>
                                                    <div class="flex-grow p-3">
                                                        <div class="font-semibold text-gray-700 text-left">
                                                            Vaga para <?= $listHire['nome_cargo'] ?>
                                                        </div>
                                                        <div class="text-sm text-gray-500 text-center">
                                                            <?= $listHire['nome_area'] ?>
                                                        </div>
                                                    </div>
                                                    <div class="p-2">
                                                        <i onclick="showHireDetail(<?= $listHire['id_vagas'] ?>)" class="fa fa-eye text-green-400 hover:text-black cursor-pointer"></i>
                                                    </div>
                                                </div>
                                            <?php }
                                        } else { ?>
                                            <h2 class="text-2xl text-white font-semibold">Nenhuma vaga disponível</h2>
                                        <?php } ?>
                                        <button class="mt-4 px-3 py-1 bg-blue-500 text-white font-medium rounded hover:bg-blue-600 focus:outline-none focus:bg-blue-500" id="close-hire"><i class="mr-2 fa fa-backward"></i> Voltar</button>
                                    </div>
                                </div>
                            </div>
                            <!--Aniversariantes-->
                            <div class="w-full h-64 mt-8 md:mx-4 rounded-md overflow-hidden bg-cover bg-center md:mt-0 md:w-1/2" style="background-image: url('https://sistemas.jhcgadvocacia.com.br/midia/roster/brian-mcgowan-WfPfeM4ek7Q-unsplash.jpg')" id="div-birth">
                                <div class="bg-gray-900 bg-opacity-50 flex items-center h-full" id="cover-birth">
                                    <div class="px-10 max-w-xl" id="roster-birth">
                                        <h2 class="text-2xl text-white font-semibold">Aniversariantes do mês</h2>
                                        <button id="btn-birth" class="flex items-center mt-4 px-3 py-2 bg-green-500 text-white text-sm uppercase font-medium rounded hover:bg-green-600 focus:outline-none focus:bg-green-500">
                                            <span>Aniversarios</span>
                                            <svg class="h-5 w-5 mx-2" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="birthday-cake" class="svg-inline--fa fa-birthday-cake fa-w-14" role="img" viewBox="0 0 448 512">
                                                <path fill="currentColor" d="M448 384c-28.02 0-31.26-32-74.5-32-43.43 0-46.825 32-74.75 32-27.695 0-31.454-32-74.75-32-42.842 0-47.218 32-74.5 32-28.148 0-31.202-32-74.75-32-43.547 0-46.653 32-74.75 32v-80c0-26.5 21.5-48 48-48h16V112h64v144h64V112h64v144h64V112h64v144h16c26.5 0 48 21.5 48 48v80zm0 128H0v-96c43.356 0 46.767-32 74.75-32 27.951 0 31.253 32 74.75 32 42.843 0 47.217-32 74.5-32 28.148 0 31.201 32 74.75 32 43.357 0 46.767-32 74.75-32 27.488 0 31.252 32 74.5 32v96zM96 96c-17.75 0-32-14.25-32-32 0-31 32-23 32-64 12 0 32 29.5 32 56s-14.25 40-32 40zm128 0c-17.75 0-32-14.25-32-32 0-31 32-23 32-64 12 0 32 29.5 32 56s-14.25 40-32 40zm128 0c-17.75 0-32-14.25-32-32 0-31 32-23 32-64 12 0 32 29.5 32 56s-14.25 40-32 40z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="max-w-xl list m-auto hidden" id="list-birth">
                                        <?php if (count($birthColab) != 0) { ?>
                                            <div class="grid grid-cols-2 md:grid-cols-3">
                                                <?php foreach ($birthColab as $birthList) { ?>
                                                    <!-- Calendar Icon -->
                                                    <div class="block rounded-t overflow-hidden  text-center m-4">
                                                        <div class="bg-blue-300 text-white py-1">
                                                            <?= $ctl->getMonthName((int)date('m')) ?>
                                                        </div>
                                                        <div class="pt-1 border-l border-r border-white bg-white">
                                                            <span class="text-5xl font-bold leading-tight">
                                                                <?= $ctl->getDayOnDate($birthList['dtnascimento_colab']) ?>
                                                            </span>
                                                        </div>
                                                        <div class="pb-2 border-l border-r border-b rounded-b-lg text-center border-white bg-white">
                                                            <span class="text-xs leading-normal px-2">
                                                                <?= $ctl->limitName($birthList['nome_colab'],3); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } else { ?>
                                            <h2 class="text-2xl text-white font-semibold">Nenhuma aniversario neste mês</h2>
                                        <?php } ?>
                                        <button class="mt-4 px-3 py-1 bg-blue-500 text-white font-medium rounded hover:bg-blue-600 focus:outline-none focus:bg-blue-500" id="close-birth"><i class="mr-2 fa fa-backward"></i> Voltar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <?php foreach ($newHire as $modalHire) { ?>
            <div class="hidden main-modal fixed w-full h-100 inset-0 z-50 overflow-hidden flex justify-center items-center animated fadeIn faster" style="background: rgba(0,0,0,.7);" id="hire<?= $modalHire['id_vagas'] ?>">
                <div class="border border-teal-500 shadow-lg modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
                    <div class="modal-content py-4 text-left px-6">
                        <!--Title-->
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-2xl font-bold">Vaga para <?= $modalHire['nome_cargo'] ?></p>
                            <div class="modal-close cursor-pointer z-50" onclick="document.getElementById('hire<?= $modalHire['id_vagas'] ?>').classList.add('hidden')">
                                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                                    <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <!--Body-->
                        <form method="POST" action="<?= URL ?>/request/declaraInteresse">
                            <div class="my-5 grid grid-cols-1" id="step-1">
                                <?= $modalHire['desc_vagas'] ?>
                            </div>
                            <input type="text" name="vaga" id="vaga" class="hidden" value="<?= $modalHire['id_vagas'] ?>" required>
                            <!--Footer-->
                            <div class="flex justify-end pt-2">
                                <button type="submit" class="inline-flex items-center justify-center px-4 py-1 space-x-1 bg-green-500 text-white rounded-md shadow hover:bg-green-600 cursor-pointer mx-2">Tenho interesse</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
        <script>
            $(document).ready(function() {
                $("#slide div:eq(0)").addClass("ativo").show();
                $(".dot").on("click", function() {
                    let id = this.id.replace(/dot/i, '');
                    setActive(id, this);
                })
            })
            var hire = false;
            var birth = false;
            var i = 1;
            var IDs = [];
            $("#dotlist").find("button").each(function() {
                IDs.push(this.id);
            });

            function clickFake() {
                setActive(IDs[i].replace(/dot/i, ''));
                i++;
                if (i > <?= count($images) ?> - 1) {
                    i = 0;
                }
            }

            function setActive(id, elem) {
                $(".ativo").fadeOut().removeClass("ativo");
                $("#image" + id).addClass("ativo");
                $(".dot").removeClass("bg-green-800").addClass("bg-green-400");
                $("#dot" + id).addClass("bg-green-800");
            }

            function hireModal(id) {
                document.getElementById('hire' + id).classList.remove('hidden');
            }

            setInterval(clickFake, 4000);

            document.getElementById("btn-hire").addEventListener("click", showHire, false);
            document.getElementById("close-hire").addEventListener("click", showHire, false);
            document.getElementById("btn-birth").addEventListener("click", showBirth, false);
            document.getElementById("close-birth").addEventListener("click", showBirth, false);

            function showHire() {
                document.getElementById('list-hire').classList.toggle('hidden');
                document.getElementById('roster-hire').classList.toggle('hidden');
                document.getElementById('div-hire').classList.toggle('h-2/4');
                document.getElementById('cover-hire').classList.toggle('p-8');
            }

            function showBirth() {
                document.getElementById('list-birth').classList.toggle('hidden');
                document.getElementById('roster-birth').classList.toggle('hidden');
                document.getElementById('div-birth').classList.toggle('overflow-hidden');
                document.getElementById('div-birth').classList.toggle('h-2/4');
                document.getElementById('cover-birth').classList.toggle('p-8');
            }

            function showHireDetail(id) {
                document.getElementById('hire' + id).classList.toggle('hidden');
            }
        </script>
    </div>
</div>