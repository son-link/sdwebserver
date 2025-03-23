class MiniDT {
	constructor(config) {
    if (!config.url || !config.target || !config.cols) return false;

    this.rows = []
    this.page = 0
    this.total = 0
    this.total_pages = 0
    this.limit = (!!config.limit) ? config.limit : 20
    this.params = (!!config.params) ? config.params : {}
    this.url = config.url
    this.cols = {}
    this.target = document.getElementById(config.target)
    this.get_data_start = !!config.get_data_start

    if (!this.target) return false

    if (!this.target.classList.contains('mini-dt')) this.target.classList.add('mini-dt')

    // Create table header, body and footer
    this.thead = this.target.createTHead()
    this.tbody = this.target.createTBody()
    this.tfoot = this.target.createTFoot()
    this.tfoot.classList.add('hide')

    // Draw header columns
    const head_row = this.thead.insertRow()

    config.cols.forEach( col => {
      const th = document.createElement('th')
      th.innerText = col.title

      if (!!col.align && ['center', 'right'].includes(col.align)) th.classList.add(`text-${col.align}`)

      // Las columnas las almacenamos de este modo para ahorrar tener que recorrer el array cada vez
      this.cols[col.col] = {
        title: col.title,
        render: (!!col.render && typeof col.render === 'function') ? col.render : null,
        align: (!!col.align && ['center', 'right'].includes(col.align)) ? col.align: null
      }

      // Añadimos la columna a la cabecera
      head_row.appendChild(th)
    });

    // Y añadimos la fila a la cabecera
    this.thead.appendChild(head_row)

    // Draw footer
    const row_footer = this.tfoot.insertRow()
    const th = document.createElement('th')
    th.setAttribute('colspan', Object.keys(this.cols).length)

    // Pagination container
    this.pagination_container = document.createElement('div')
    this.pagination_container.classList.add('pagination')

    // Pagination text
    this.pageText = document.createTextNode(`Page 1 of 1`)
    this.pagination_container.appendChild(this.pageText)

    // Previous page button
    this.prev_btn = document.createElement('button')
    //this.prev_btn.innerText = '<'
    this.prev_btn.classList.add('prev-btn')
    this.prev_btn.addEventListener('click', this.previous)
    this.pagination_container.appendChild(this.prev_btn)

    // Page selector
    this.pageSelector = document.createElement('select')
    this.pageSelector.classList.add('page-selector')
    this.pageSelector.addEventListener('change', this.changePage)
    this.pagination_container.appendChild(this.pageSelector)

    // Next page button
    this.next_btn = document.createElement('button')
    //this.next_btn.innerText = '>'
    this.next_btn.classList.add('next-btn')
    this.next_btn.addEventListener('click', this.next)
    this.pagination_container.appendChild(this.next_btn)

    th.appendChild(this.pagination_container)
    row_footer.appendChild(th);
    
    if (this.get_data_start) this.getData()
  }

  getData = async (params=null) => {
    this.rows = []
    this.params.limit = this.limit
    this.params.page = this.page

    if (!params) params = this.params

    try {
      const resp = await fetch(this.url + '?' + new URLSearchParams(params).toString())
      const data = await resp.json()
      if (!!data.data) this.rows = data.data
      this.total = (!!data.total) ? data.total : 0

      if (this.total > 0 && this.total > this.limit) this.total_pages = Math.ceil(this.total / this.limit)
      else this.total_pages = 1

      this.pageText.textContent = `Page ${this.page + 1} of ${this.total_pages}`
    } catch (err) {
      console.error(err);
    }

    this.render()
  }

  /**
   * Rendering the component
   */
  render = () => {
    this.tbody.innerHTML = ''

    if (this.rows.length == 0) {
      const row = this.tbody.insertRow()
      const td = document.createElement('td')
      td.setAttribute('colspan', Object.keys(this.cols).length)
      td.innerHTML = '<strong>No data</strong>'
      row.appendChild(td)
      this.tfoot.classList.add('hide')
    } else {
      this.rows.forEach( row => {
        const tr = this.tbody.insertRow()

        Object.keys(this.cols).forEach( key => {
          if (key in row) {
            const cell = tr.insertCell()
            cell.setAttribute('data-title', this.cols[key].title)
            if (!!this.cols[key].align) cell.classList.add(`text-${this.cols[key].align}`)

            cell.innerHTML = (!!this.cols[key].render) 
              ? this.cols[key].render(row)
              : cell.innerText = row[key]
          }
        })
      })
      this.tfoot.classList.remove('hide')
    }

    this.pageSelector.removeEventListener('change', this.changePage)
    this.pageSelector.innerHTML = ''
  
    for (let i=0; i < this.total_pages; i++) {
      this.pageSelector.append(new Option(i + 1, i, i == this.page))
    }

    this.pageSelector.value = this.page
    this.pageSelector.addEventListener('change', this.changePage)
  }

  changePage = (value) => {
    this.page = (value instanceof Event) ? parseInt(value.target.value) : parseInt(value)
    this.pageText.textContent = `Page ${this.page + 1} of ${this.total_pages}`
    this.getData()
  }

  previous = () => {
    if (this.page > 0) this.page--
    this.pageText.textContent = `Page ${this.page + 1} of ${this.total_pages}`
    this.getData()
  }

  next = () => {
    if (this.page < this.total_pages - 1) this.page++
    this.pageText.textContent = `Page ${this.page + 1} of ${this.total_pages}`
    this.getData()
  }
}